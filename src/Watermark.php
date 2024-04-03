<?php
/**
 * Watermark plugin for Craft CMS 3.x
 *
 * Watermark your images
 *
 * @link      https://bryglab.io
 * @package   Watermark
 * @since     1.0.0
 */
namespace bryglab\watermark;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\helpers\FileHelper;
use bryglab\watermark\models\SettingsModel;
use bryglab\watermark\twigextensions\WatermarkTwigExtension;
use yii\base\Event;
use yii\base\InvalidConfigException;

/**
 * Class Watermark
 * @package bryglab\watermark
 *
 * @property-read SettingsModel $settings
 */
class Watermark extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    public static string $directory = '';

    public static function config(): array
    {
        return ['components' => [
            // Define component configs here...
        ],];
    }

    public function init(): void
    {
        parent::init();

        // Used to store the directory where the watermarked images are stored
        Watermark::$directory = $this->getSettings()->directory;

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function () {
            $this->attachEventHandlers();
        });

        // Register the Twig extension
        Craft::$app->view->registerTwigExtension(new WatermarkTwigExtension());

    }

    /**
     * @throws InvalidConfigException
     */
    protected function createSettingsModel(): ? Model
    {
        return Craft::createObject(SettingsModel::class);
    }

    protected function settingsHtml(): ?string
    {
        $settings = $this->getSettings();

        // to load currently selected assets
        $images = [];
        if ($settings->watermarkImage) {
            foreach ($settings->watermarkImage as $watermark) {
                $images[] = Craft::$app->elements->getElementById($watermark);
            }
        }

        return Craft::$app->view->renderTemplate('watermark/_settings.twig', ['plugin' => $this, 'settings' => $settings, 'images' => $images]);

    }

    /**
     * Attach event handlers
     */
    private function attachEventHandlers(): void
    {

        // remove the old directory and create a new one if the directory has changed
        Event::on(Plugin::class, Plugin::EVENT_AFTER_SAVE_SETTINGS, function (Event $event) {
            $settings = $this->getSettings();
            $baseDirectory = explode('/', Watermark::$directory);
            $oldPath = Craft::getAlias('@webroot/' . $baseDirectory[0]);
            $newPath = Craft::getAlias('@webroot/' . $settings->directory);

            // If the directory has changed, remove the old directory and create a new one
            if (Watermark::$directory !== $settings->directory) {
                FileHelper::removeDirectory($oldPath);
                FileHelper::createDirectory($newPath);
            } else {
                if (is_dir($oldPath)) {
                    FileHelper::clearDirectory($oldPath);
                }
            }
        });

        // remove all watermarked images if the asset index data is deleted
        // TODO: This is not working yet
        /*
        Event::on(ImageTransforms::class, ImageTransforms::EVENT_BEFORE_INVALIDATE_ASSET_TRANSFORMS, function (Event $event) {
            $path = Craft::getAlias('@webroot/' . Watermark::$directory);
            // throw new \Exception($path);
            FileHelper::clearDirectory($path);
        });
        */

    }
}
