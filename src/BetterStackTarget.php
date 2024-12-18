<?php
namespace arcanewebdesign\craftbetterstacklogger;

use Craft;
use craft\base\Model;
use Exception;
use Monolog\Handler\Curl\Util;

class BetterStackTarget extends \yii\log\Target
{
    // Public Properties
    // =========================================================================

	/**
     * @var ?string
     */
    public ?string $sourceToken = null;

	/**
	 * @var bool
	 */
	public bool $enabled = false;

	/**
	 * @var array
	 */
	public array $exceptCodes = [403, 404];

	/**
	 * @var Model
	 */
	public Model $settings;

	public function __construct(Model $settings) {
		$this->settings = $settings;
		$this->enabled = $settings->enabled;
		$this->sourceToken = $settings->sourceToken;
		$this->exceptCodes = $settings->exceptCodes;
		$this->setLevels($settings->levels);
		$this->logVars = $settings->logVars;
	}

	// Customize the method to format and send logs to BetterStack
    public function export()
    {
	    if (!$this->enabled || !$this->levels) { return; }
		$bail = false;

		foreach ($this->messages as $message) {
			[$text, $level, $category, $timestamp] = $message;

			// Check for status codes by analyzing the category or message content
			if (is_string($this->exceptCodes)) {
				$codesArray = explode(",", $this->exceptCodes);
			} else {
				if (sizeof($this->exceptCodes) === 1 && str_contains($this->exceptCodes[0], ",")) {
					$codesArray = explode(",", $this->exceptCodes[0]);
				} else {
					$codesArray = $this->exceptCodes;
				}
			}
			foreach ($codesArray as $code) {
				$code = trim($code);
				if ($code !== "") {
					if (str_contains($text, $code) || $category === "yii\web\HttpException:{$code}") {
						// Skip messages with this code
						$bail = true;
					}
				}
			}

			// Send each log message to BetterStack here
			if (!$bail) {
				$this->sendToBetterStack($message, $this->sourceToken);
			}
		}
    }

    protected function sendToBetterStack($message, $sourceToken)
    {
	    [$text, $level, $category, $timestamp] = $message;
		$data = json_encode([
			'message' => $text,
			'level' => $level,
			'category' => $category,
			'timestamp' => $timestamp,
		]);

	    $handle = \curl_init();
	    $headers = [
		    'Content-Type: application/json',
		    "Authorization: Bearer " . $sourceToken,
	    ];

	    \curl_setopt($handle, CURLOPT_URL, "https://in.logs.betterstack.com");
	    \curl_setopt($handle, CURLOPT_POST, true);
	    \curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
	    \curl_setopt($handle, CURLOPT_CONNECTTIMEOUT_MS, 5000);
	    \curl_setopt($handle, CURLOPT_TIMEOUT_MS, 5000);

	    \curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
	    \curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

	    try {
		    Util::execute($handle, 5, false);
	    } catch (Exception $e) {
		    print(" ... exception! " . $e->getMessage());
	    }
    }

	/**
	 * @inheritdoc
	 */
	public function setLevels($levels): void
	{
		if (is_array($levels)) {
			foreach ($levels as $key => $level) {
				if (!in_array($level, ['error', 'warning'])) {
					unset($levels[$key]);
				}
			}
		}
		parent::setLevels($levels);
	}
    
}