<?php

namespace billmn\exchanger\console\controllers;

use Craft;
use craft\commerce\Plugin as Commerce;
use craft\console\Controller;
use craft\helpers\Json;
use GuzzleHttp\Client;
use yii\console\ExitCode;

/**
 * Update controller
 */
class UpdateController extends Controller
{
    public $defaultAction = 'index';

    public function options($actionID): array
    {
        $options = parent::options($actionID);
        switch ($actionID) {
            case 'index':
                // $options[] = '...';
                break;
        }
        return $options;
    }

    /**
     * exchanger/update command
     */
    public function actionIndex(): int
    {
        $currencyService = Commerce::getInstance()->getPaymentCurrencies();

        $primaryCurrency = $currencyService->getPrimaryPaymentCurrency();
        $nonPrimaryCurrencies = $currencyService->getNonPrimaryPaymentCurrencies();

        if (count($nonPrimaryCurrencies) === 0) {
            $this->note('There are no non-primary currencies');

            return ExitCode::OK;
        }

        $symbols = array_keys($nonPrimaryCurrencies);

        $response = (new Client)->get('https://api.exchangerate.host/latest', [
            'query' => [
                'base' => $primaryCurrency->iso,
                'symbols' => implode(',', $symbols),
                'places' => 2,
            ],
        ]);

        $body = Json::decodeIfJson($response->getBody());

        if ($body['success'] === false) {
            $this->failure('Unable to retrieve exchange rates');

            return ExitCode::UNSPECIFIED_ERROR;
        }

        foreach ($nonPrimaryCurrencies as $currency) {
            $rate = $body['rates'][$currency->iso] ?? false;

            if (is_numeric($rate)) {
                $currency->rate = $rate;
                $currencyService->savePaymentCurrency($currency);
            }
        }

        $this->success('Exchange rates updated successfully');

        return ExitCode::OK;
    }
}
