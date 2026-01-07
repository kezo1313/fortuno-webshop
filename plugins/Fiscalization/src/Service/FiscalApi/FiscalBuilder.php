<?php declare(strict_types=1);

// IMPORTANT: The namespace must match the folder structure
namespace Fortuno\Fiscalization\Service\FiscalApi;

class FiscalBuilder
{
    private array $data = ['pdvs' => []];

    public function setDateTime(\DateTimeInterface $dt): self {
        $this->data['date_time_of_invoice'] = $dt->format('Y-m-d\TH:i:s');
        return $this;
    }

    public function setInvoiceNumber(string $val): self { $this->data['invoice_number'] = $val; return $this; }
    
    // ... (rest of the methods) ...

    public function setBusinessPremiseLabel(string $val): self { $this->data['business_premise_label'] = $val; return $this; }
    public function setBillingDeviceLabel(string $val): self { $this->data['billing_device_label'] = $val; return $this; }
    public function setTotalAmount(float $val): self { $this->data['total_amount'] = number_format($val, 2, '.', ''); return $this; }
    public function setOperatorOIB(string $val): self { $this->data['operator_oib'] = $val; return $this; }
    public function setPaymentMethod(string $val): self { $this->data['payment_method'] = $val; return $this; }
    
    public function addPDV(float $rate, float $base, float $amount): self {
        $this->data['pdvs'][] = [
            'percentage' => number_format($rate, 2, '.', ''),
            'base' => number_format($base, 2, '.', ''),
            'amount' => number_format($amount, 2, '.', '')
        ];
        return $this;
    }

    public function build(): array {
        if (empty($this->data['invoice_number'])) throw new \Exception("Missing invoice number");
        return $this->data;
    }
}