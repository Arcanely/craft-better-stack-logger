<?php

/**
 * @author    arcane-web-design
 * @package   craft-better-stack-logger
 * @since     1.0.0
 */

/**
 * BetterStack Logger config.php
 *
 * This file exists only as a template for the BetterStack Logger settings.
 * It does nothing on its own.
 *
 * Don't edit this file, instead copy it to 'craft/config' as 'better-stack-logger.php'
 * and make your changes there to override default settings.
 *
 * Once copied to 'craft/config', this file will be multi-environment aware as
 * well, so you can have different settings groups for each environment, just as
 * you do for 'general.php'
 */

use craft\helpers\App;

return [
	'*' => [
		'enabled' => false,
		'sourceToken' => App::env('BETTERSTACK_SOURCE_TOKEN'),
		'levels' => ['error', 'warning'],
		'logVars' => [],
		'exceptCodes' => [403, 404],
	],

	'staging' => [
		'enabled' => true,
	],

	'production' => [
		'enabled' => true,
	],
];