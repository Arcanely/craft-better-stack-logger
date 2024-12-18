<?php

namespace arcanewebdesign\craftbetterstacklogger;

use Craft;
use arcanewebdesign\craftbetterstacklogger\models\Settings;
use craft\base\Model;
use craft\base\Plugin;

/**
 * BetterStackLogger plugin
 *
 * @method static BetterStackLogger getInstance()
 * @method Settings getSettings()
 * @author Nick Adams (Arcane Web Design) <hello@arcane-web-design.com>
 * @copyright Nick Adams (Arcane Web Design)
 * @license MIT
 */
class BetterStackLogger extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    public function init(): void
    {
        parent::init();

		// Define a route for sending test log
		Craft::$app->getUrlManager()->addRules([
            'better-stack-logger/send-test-log' => 'better-stack-logger/log/send-test-log',
        ], false);

        $settings = $this->getMergedSettings();

		if ($settings && $settings->validate()) {

			$betterStackTarget = new BetterStackTarget($settings);

			$dispatcher = Craft::$app->getLog();
		    $dispatcher->targets = array_merge($dispatcher->targets, ['betterstack' => $betterStackTarget]);
		}
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
	    $overrides = Craft::$app->getConfig()->getConfigFromFile($this->handle);
        return Craft::$app->view->renderTemplate('better-stack-logger/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
	        'overrides' => $overrides,
        ]);
    }

	protected function getMergedSettings() {
		$settings = $this->getSettings();
		$config = Craft::$app->getConfig()->getConfigFromFile($this->handle);
		foreach ($config as $key => $value) {
			$settings->$key = $value;
		}
		return $settings;
	}

}
