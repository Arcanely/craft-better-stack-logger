<?php
namespace arcanewebdesign\craftbetterstacklogger\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;

class LogController extends Controller
{
    public function actionSendTestLog(): Response
    {
        try {
	        Craft::warning('This is an warning test', 'Test');
			Craft::error('This is an error test', 'Test');
	        Craft::info('This is an info test', 'Test');
			Craft::debug('This is a debug test', 'Test');
            return $this->asJson([ 'success' => true ]);
        } catch (\Exception $e) {
            Craft::error('Failed to send test log: ' . $e->getMessage(), __METHOD__);
            return $this->asJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}