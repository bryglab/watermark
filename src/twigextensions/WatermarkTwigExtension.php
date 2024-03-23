<?php

namespace stefanladner\craftwatermark\twigextensions;

use Craft;
use Imagick;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use yii\base\InvalidConfigException;

class WatermarkTwigExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('watermark', [$this, 'watermark']),
        ];
    }

    /**
     * @throws \ImagickException
     * @throws InvalidConfigException
     */
    public function watermark($image, $transform = null): string
    {
        // Get the watermark image ID from the plugin settings
        $watermarkId        = Craft::$app->plugins->getPlugin('watermark')->getSettings()->imageId[0];
        $watermarkAsset     = Craft::$app->assets->getAssetById($watermarkId);
        $watermarkFsPath    = Craft::getAlias($watermarkAsset->getVolume()->fs->path);
        $watermark          = $watermarkFsPath . DIRECTORY_SEPARATOR . $watermarkAsset->getPath();
        $watermark          = new \Imagick($watermark);

        if (extension_loaded('imagick'))
            {
                // Get the asset
                $asset = Craft::$app->assets->getAssetById($image->id);
                $fsPath = Craft::getAlias($asset->getVolume()->fs->path);
                $attachment = $fsPath . DIRECTORY_SEPARATOR . $asset->getPath();

                // Create an Imagick object for the image
                $imagick = new \Imagick($attachment);

                $imagick->resizeImage(500, 300, Imagick::FILTER_LANCZOS, 1);
                $imagick->compositeImage($watermark, Imagick::COMPOSITE_OVER, 15, 15);

                $newImagePath = '/Users/stefanladner/htdocs/plugins.local/web/test.jpg'; // Update this path as per your requirement
                $imagick->writeImage($newImagePath);

                /*
                 * https://craftcms.stackexchange.com/questions/39241/creating-an-asset-from-an-api-using-a-module
                 *
                $asset = new Asset();
                $asset->tempFilePath = $tempPath;
                $asset->filename = $fileName;
                $asset->folderId = null;
                $asset->newFolderId = $folder->id;
                $asset->kind = "Image";
                $asset->title = $assetTitle;
                $asset->avoidFilenameConflicts = true;
                $asset->setVolumeId($folder->volumeId);
                $asset->setScenario(Asset::SCENARIO_CREATE);

                $asset->validate();
                Craft::$app->getElements()->saveElement($asset, false);*/

                // Return the image with the watermark
                //return Craft::$app->assetManager->getAssetUrl($newImagePath);

                return '/test.jpg';

        }

        if (extension_loaded('gd')) {
            $image = imagecreatefromstring(file_get_contents($input));
            $watermark = imagecreatefromstring(file_get_contents(Craft::$app->plugins->getPlugin('watermark')->getSettings()->imageId[0]));
            imagecopy($image, $asset, 0, 0, 0, 0, imagesx($watermark), imagesy($watermark));
            imagepng($image, $input);
        }

        return $image . $transform;
    }
}