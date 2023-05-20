<?php

namespace billmn\exchanger\console\controllers;

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
    public ?string $driver = 'exchange-rate';

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
        $this->note('DRIVER: ' . $this->driver);

        $driverClass = Exchanger::getInstance()->driver($this->driver);

        $updated = $driverClass->updateRates();

        if (count($updated) === 0) {
            $this->failure('No conversion rate has been updated');

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $tableRows = $updated->map(fn($currency) => [
            $currency['iso'],
            $currency['rate'],
        ]);

        $this->table(['CURRENCY', 'RATE'], $tableRows->toArray());

        $this->success('Done');

        return ExitCode::OK;
    }
}
