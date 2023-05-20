<?php

namespace billmn\exchanger\drivers;

use craft\helpers\Json;
use Exception;
use GuzzleHttp\Client;

class ExchangeRate extends Driver
{
    public function getRates(): array
    {
        $symbols = implode(',', $this->getNonPrimaryCurrenciesIso());

        $response = (new Client())->get('https://api.exchangerate.host/latest', [
            'query' => [
                'base' => $this->getPrimaryCurrencyIso(),
                'amount' => 1,
                'symbols' => $symbols,
            ],
        ]);

        $body = Json::decodeIfJson($response->getBody());

        if (is_string($body)) {
            throw new Exception('Unable to process the API response');
        }

        return $body['rates'];
    }
}
