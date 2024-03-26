<?php

namespace bryglab\watermark\controllers;

use Craft;
use craft\helpers\FileHelper;
use yii\base\ErrorException;
use yii\web\Controller;
use bryglab\watermark\Watermark;

/**
 * Class WatermarkController
 * @package bryglab\watermark\controllers
 * @noinspection PhpUnused
 */
class WatermarkController extends Controller
{
    /**
     * Clear the directory
     * @return bool
     * @throws ErrorException
     * @noinspection PhpUnused
     */
    public function actionClear(): true
    {
        // Clear the directory from backend settings
        $directory = Watermark::getInstance()->getSettings()->directory;
        $directory = Craft::getAlias('@webroot') . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR;
        FileHelper::clearDirectory($directory);
        return true;
    }
}