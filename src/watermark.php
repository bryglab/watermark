<?php

namespace stefanladner\craftwatermark;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\records\VolumeFolder;
use stefanladner\craftwatermark\models\Settings;
use stefanladner\craftwatermark\twigextensions\WatermarkTwigExtension;
use yii\base\Event;

/**
 * Watermark plugin
 *
 * @method static watermark getInstance()
 * @method Settings getSettings()
 * @author Stefan Ladner <stefan.ladner@gmail.com>
 * @copyright Stefan Ladner
 * @license https://craftcms.github.io/license/ Craft License
 */

class watermark extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

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

        Craft::$app->view->registerTwigExtension(new WatermarkTwigExtension());

    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {

        $settings = $this->getSettings();
        // to load currently selected assets
        $images = [];
        if ($settings->imageId) {
            foreach ($settings->imageId as $imgId) {
                $images[] = Craft::$app->elements->getElementById($imgId);
            }
        }
        // to limit asset selection to one folder
        $volume = Craft::$app->volumes->getVolumeByHandle('images');//it could be the volume handle you created before
        $volFolderRecord = VolumeFolder::findOne([
            'volumeId' => $volume->id
        ]);
        $folderUid = $volFolderRecord->uid;
        return Craft::$app->view->renderTemplate('watermark/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
            'images' => $images,
            'folderUid' => $folderUid
        ]);

    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
        // (see https://craftcms.com/docs/4.x/extend/events.html to get started)
    }
}
