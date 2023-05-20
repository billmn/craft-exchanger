<?php

namespace billmn\exchanger\drivers;

use craft\commerce\models\PaymentCurrency;
use craft\commerce\Plugin as Commerce;
use craft\commerce\services\PaymentCurrencies;
use Illuminate\Support\Collection;

abstract class Driver
{
    abstract public function getRates(): array;

    public function getCurrencyService(): PaymentCurrencies
    {
        return Commerce::getInstance()->getPaymentCurrencies();
    }

    public function getPrimaryCurrency(): ?PaymentCurrency
    {
        return $this->getCurrencyService()->getPrimaryPaymentCurrency();
    }

    public function getPrimaryCurrencyIso(): ?string
    {
        return $this->getPrimaryCurrency()?->iso;
    }

    public function getNonPrimaryCurrencies(): array
    {
        return $this->getCurrencyService()->getNonPrimaryPaymentCurrencies();
    }

    public function getNonPrimaryCurrenciesIso(): array
    {
        return array_keys($this->getNonPrimaryCurrencies());
    }

    public function updateRates(): Collection
    {
        $rates = $this->getRates();
        $updated = [];

        foreach ($this->getNonPrimaryCurrencies() as $currency) {
            $rate = $rates[$currency->iso] ?? false;

            if (is_numeric($rate)) {
                $currency->rate = $rate;

                if ($this->getCurrencyService()->savePaymentCurrency($currency)) {
                    $updated[] = [
                        'iso' => $currency->iso,
                        'rate' => $currency->rate,
                    ];
                }
            }
        }

        return Collection::make($updated);
    }
}
