<?php

namespace arcanewebdesign\craftbetterstacklogger\models;

use craft\base\Model;

/**
 * BetterStackLogger settings
 */
class Settings extends Model
{
	/**
     * @var bool Should we enable logs to be sent to Better Stack
     */
    public bool $enabled = false;

	/**
     * @var string the source token for Better Stack
     */
    public ?string $sourceToken = null;

	/**
	 * @var array
	 */
	public array $levels = ['error', 'warning'];

	/**
	 * @var array
	 */
	public array $exceptCodes = [403, 404];

	/**
	 * @var array
	 */
	public array $logVars = [];
}