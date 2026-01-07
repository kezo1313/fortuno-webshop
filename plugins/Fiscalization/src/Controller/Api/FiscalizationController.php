<?php declare(strict_types=1);

namespace Fortuno\Fiscalization\Controller\Api;

use Fortuno\Fiscalization\Service\FiscalizationService;
use Shopware\Core\Framework\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route; // Novi import za atribute

#[Route(defaults: ['_routeScope' => ['api']])]
class FiscalizationController extends AbstractController
{
    public function __construct(private readonly FiscalizationService $service) {}

    #[Route(path: '/api/_action/fortuno/fiscalize/{orderId}', name: 'api.action.fortuno.fiscalize', methods: ['POST'])]
    public function fiscalize(string $orderId, Context $context): JsonResponse
    {
        try {
            $data = $this->service->fiscalizeOrder($orderId, $context);
            return new JsonResponse(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}