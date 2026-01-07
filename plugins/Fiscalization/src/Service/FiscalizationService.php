<?php declare(strict_types=1);

namespace Fortuno\Fiscalization\Service;

use Fortuno\Fiscalization\Service\FiscalApi\FiscalApiClient;
use Fortuno\Fiscalization\Service\FiscalApi\FiscalBuilder;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Psr\Log\LoggerInterface;

class FiscalizationService
{
    public function __construct(
        private readonly SystemConfigService $configService,
        private readonly EntityRepository $orderRepository,
        private readonly LoggerInterface $logger
    ) {}

    public function fiscalizeOrder(string $orderId, Context $context): array
    {
        // 1. Dohvat konfiguracije
        $config = $this->configService->get('FortunoFiscalization.config');

        // Sigurna provjera je li omoguceno (default false ako key ne postoji)
        if (empty($config['enabled'])) {
            // Nije greška, samo info da je ugašeno
            throw new \Exception('Fiskalizacija je isključena u postavkama.');
        }

        // 2. Validacija konfiguracije (da ne puca na undefined array key)
        $apiEndpoint = $config['apiEndpoint'] ?? null;
        $companyOib  = $config['companyOib'] ?? null;
        $apiToken    = $config['apiToken'] ?? null;
        
        // Default vrijednosti ako nisu unesene
        $businessPremise = $config['businessPremise'] ?? 'POSL1';
        $deviceLabel     = $config['deviceLabel'] ?? '1';

        // Provjera obaveznih polja
        $missingFields = [];
        if (empty($apiEndpoint)) $missingFields[] = 'apiEndpoint';
        if (empty($companyOib))  $missingFields[] = 'companyOib';
        if (empty($apiToken))    $missingFields[] = 'apiToken';

        if (!empty($missingFields)) {
            $msg = 'Konfiguracija fiskalizacije nije potpuna. Nedostaje: ' . implode(', ', $missingFields);
            
            // Logiramo grešku u sistemski log
            $this->logger->error($msg, ['orderId' => $orderId]);
            
            // Bacamo Exception koji će admin vidjeti kao notifikaciju
            throw new \Exception($msg);
        }

        // 3. Dohvat narudžbe
        $criteria = new \Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria([$orderId]);
        $criteria->addAssociation('price');
        $criteria->addAssociation('deliveries'); 
        
        /** @var OrderEntity|null $order */
        $order = $this->orderRepository->search($criteria, $context)->first();
        
        if (!$order) {
            $this->logger->error("Fiskalizacija neuspjela: Narudžba $orderId nije pronađena.");
            throw new \Exception('Narudžba nije pronađena.');
        }

        try {
            // 4. Inicijalizacija klijenta
            $client = new FiscalApiClient(
                (string)$apiEndpoint,
                (string)$companyOib,
                (string)$apiToken,
                $this->logger
            );

            // 5. Izgradnja zahtjeva
            $builder = new FiscalBuilder();
            $builder->setDateTime($order->getOrderDateTime())
                    ->setInvoiceNumber((string)$order->getOrderNumber())
                    ->setBusinessPremiseLabel((string)$businessPremise)
                    ->setBillingDeviceLabel((string)$deviceLabel)
                    ->setTotalAmount($order->getPrice()->getTotalPrice())
                    ->setOperatorOIB((string)$companyOib)
                    ->setPaymentMethod('K'); // TODO: Implementirati mapiranje načina plaćanja

            foreach ($order->getPrice()->getCalculatedTaxes() as $tax) {
                $builder->addPDV($tax->getTaxRate(), $tax->getPrice(), $tax->getTax());
            }
            
            // Fallback ako nema poreza (npr. export) - OPREZ: API možda traži stavke
            if ($order->getPrice()->getCalculatedTaxes()->count() === 0) {
                 // Logika za oslobođenje
            }

            // 6. Slanje na API
            $result = $client->processInvoice($builder->build());

            $jir = $result['data']['Jir'] ?? null;
            $zki = $result['data']['Zki'] ?? null;

            

            if ($jir) {
                $this->orderRepository->update([[
                    'id' => $orderId,
                    'customFields' => [
                        'fortuno_fiscal_jir' => $jir,
                        'fortuno_fiscal_zki' => $zki,
                        'fortuno_fiscal_date' => (new \DateTime())->format('Y-m-d H:i:s')
                    ]
                ]], $context);
            } else {
                // Ako je success true, ali nema JIR-a (čudna situacija)
                throw new \Exception('API je vratio uspjeh, ali nedostaje JIR.');
            }

            return ['jir' => $jir, 'zki' => $zki];

        } catch (\Exception $e) {
            // Logiramo stvarni stack trace za developere
            $this->logger->critical('Fiskalizacija Exception: ' . $e->getMessage(), [
                'orderId' => $orderId,
                'trace' => $e->getTraceAsString()
            ]);

            // Ponovno bacamo exception da admin vidi poruku
            throw $e;
        }
    }
}