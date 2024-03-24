<?php

namespace stefanladner\craftwatermark;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\ModelEvent;
use craft\helpers\FileHelper;
use craft\records\VolumeFolder;
use stefanladner\craftwatermark\models\SettingsModel;
use stefanladner\craftwatermark\twigextensions\WatermarkTwigExtension;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Event;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * Watermark plugin
 *
 * @method static watermark getInstance()
 * @method SettingsModel getSettings()
 * @author Stefan Ladner <stefan.ladner@gmail.com>
 * @copyright Stefan Ladner
 * @license https://craftcms.github.io/license/ Craft License
 */

class Watermark extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    public static $directory;

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

        // Used to store the directory where the watermarked images are stored
        Watermark::$directory = $this->getSettings()->directory;

        Craft::$app->onInit(function() {
            $this->attachEventHandlers();
        });

        Craft::$app->view->registerTwigExtension(new WatermarkTwigExtension());

    }

    /**
     * @throws InvalidConfigException
     */
    protected function createSettingsModel(): ? Model
    {
        return Craft::createObject(SettingsModel::class);
    }

    /**
     * @throws SyntaxError
     * @throws Exception
     * @throws RuntimeError
     * @throws LoaderError
     */
    protected function settingsHtml(): ? string
    {
        $settings = $this->getSettings();

        // to load currently selected assets
        $images = [];
        if ($settings->imageId) {
            foreach ($settings->imageId as $imgId) {
                $images[] = Craft::$app->elements->getElementById($imgId);
            }
        }
        return Craft::$app->view->renderTemplate('watermark/_settings.twig', [
            'plugin' => $this,
            'settings' => $settings,
            'images' => $images,
            //'folderUid' => $folderUid
        ]);

    }

    private function attachEventHandlers(): void
    {

        Event::on(
            Plugin::class,
            Plugin::EVENT_AFTER_SAVE_SETTINGS,
            function (Event $event) {
                $settings = $this->getSettings();
                $baseDirectory = explode('/', Watermark::$directory);
                $oldPath = Craft::getAlias( '@webroot/' . $baseDirectory[0]);
                $newPath = Craft::getAlias('@webroot/' . $settings->directory);
                if (Watermark::$directory !== $settings->directory) {
                    FileHelper::removeDirectory($oldPath);
                    FileHelper::createDirectory($newPath);
                } else {
                    FileHelper::clearDirectory($oldPath);
                }
            }
        );

    }
}
