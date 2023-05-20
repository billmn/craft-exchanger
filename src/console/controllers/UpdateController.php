<?php

namespace billmn\exchanger\console\controllers;

use billmn\exchanger\drivers\FetchRatesException;
use billmn\exchanger\Exchanger;
use craft\console\Controller;
use yii\console\ExitCode;

/**
 * Update controller
 */
class UpdateController extends Controller
{
    public $defaultAction = 'index';

    /**
     * @var string|null Exchange driver.
     */
    public ?string $driver = 'exchange_rate';

    public function options($actionID): array
    {
        $options = parent::options($actionID);

        $options[] = 'driver';

        return $options;
    }

    /**
     * exchanger/update command
     */
    public function actionIndex(): int
    {
        $driverClass = Exchanger::getInstance()->driver($this->driver);

        if (count($driverClass->getNonPrimaryCurrencies()) === 0) {
            $this->note('There are no non-primary currencies');

            return ExitCode::OK;
        }

        try {
            $driverClass->updateRates();
        } catch (FetchRatesException $e) {
            $this->failure($e->getMessage());

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->success('Exchange rates updated successfully');

        return ExitCode::OK;
    }
}
