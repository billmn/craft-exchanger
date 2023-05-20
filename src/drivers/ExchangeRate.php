<?php

namespace billmn\exchanger\drivers;

use craft\helpers\Json;

class ExchangeRate extends Driver
{
    public function getRates(): array
    {
        $response = $this->getClient()->get('https://api.exchangerate.host/latest', [
            'query' => [
                'base' => $this->getPrimaryCurrencyIso(),
                'symbols' => implode(',', $this->getNonPrimaryCurrenciesIso()),
            ],
        ]);

        $body = Json::decodeIfJson($response->getBody());

        if ($body['success'] === false) {
            throw new FetchRatesException();
        }

        return $body['rates'];
    }
}
