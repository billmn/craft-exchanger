<?php

namespace billmn\exchanger;

use billmn\exchanger\drivers\Driver;
use billmn\exchanger\models\Settings;
use Craft;
use craft\base\Plugin;
use craft\helpers\StringHelper;

/**
 * Exchanger plugin
 *
 * @method static Exchanger getInstance()
 * @method Settings getSettings()
 * @author Davide Bellini <bellini.davide@gmail.com>
 * @copyright Davide Bellini
 * @license MIT
 */
class Exchanger extends Plugin
{
    public string $schemaVersion = '1.0.0';

    public static function config(): array
    {
        return [
            'components' => [
                // Define component configs here...
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->attachEventHandlers();
            // ...
        });
    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
        // (see https://craftcms.com/docs/4.x/extend/events.html to get started)
    }

    public function driver(string $name): ?Driver
    {
        $class = __NAMESPACE__ . "\\drivers\\" . StringHelper::toPascalCase($name);

        return new $class();
    }
}
