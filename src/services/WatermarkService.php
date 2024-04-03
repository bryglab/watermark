<?php

namespace bryglab\watermark\services;

use Craft;
use craft\base\Component;
use Imagine\Imagick\Imagick;
use bryglab\watermark\Watermark;
use bryglab\watermark\models\WatermarkModel;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 * Watermark service
 * @package bryglab\watermark\services
 */
class WatermarkService extends Component
{

    /**
     * @param $image
     * @param $options
     * @return string
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function createWatermark($image, $options): string
    {
        // Create the watermarked image
        $model = new WatermarkModel();

        // define options

        if (Watermark::getInstance()->getSettings()->bestFit) {
            $settingsMode = 'fit';
        } else {
            $settingsMode = 'stretch';
        }
        $watermarkId = $options['asset']->id ?? Watermark::getInstance()->getSettings()->watermarkImage[0];
        $watermarkWidth = $options['width'] ?? Watermark::getInstance()->getSettings()->watermarkWidth;
        $watermarkHeight = $options['height'] ?? Watermark::getInstance()->getSettings()->watermarkHeight;
        $quality = $options['quality'] ?? Watermark::getInstance()->getSettings()->quality;
        $position = $options['position'] ?? Watermark::getInstance()->getSettings()->position;
        $format = $options['format'] ?? Watermark::getInstance()->getSettings()->format;
        $padding = $options['padding'] ?? Watermark::getInstance()->getSettings()->padding;
        $mode = $options['mode'] ?? $settingsMode;

        // TODO maybe put variables in array?

        $directory = $model->getAbsoluteDirectory();
        $filename = md5($image->id) . '.' . $format;
        $newImagePath = $directory . md5($image->id) . '.' . $format;

        // get watermark asset
        $watermarkAsset = Craft::$app->assets->getAssetById($watermarkId);

        // transform watermark and get path
        $watermarkTransform = array(
            'mode' => $mode,
            'width' => $watermarkWidth,
            'height' => $watermarkHeight,
            'format' => 'png'
        );
        $watermark = $watermarkAsset->getUrl($watermarkTransform, true);
        $watermark = str_replace(Craft::$app->sites->getCurrentSite()->baseUrl, '', $watermark);
        $watermark = Craft::getAlias('@webroot') . DIRECTORY_SEPARATOR . $watermark;

        // define asset paths
        if (isset($options['transform'])) {
            $asset = $image->getUrl($options['transform'], true);
            $asset = str_replace(Craft::$app->sites->getCurrentSite()->baseUrl, '', $asset);
            $asset = Craft::getAlias('@webroot') . DIRECTORY_SEPARATOR . $asset;
        } else {
            $asset = Craft::getAlias($image->getVolume()->fs->path);
            $asset = $asset . DIRECTORY_SEPARATOR . $image->getPath();
        }

        // TODO IMPORTANT simplify variable names, bloody chaos here. urgh!

        // check if directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
        // create watermark asset
        if (extension_loaded('imagick')) {
            return $this->createFromImagix($watermark, $asset, $newImagePath, $position, $padding, $quality, $format, $model, $filename);
        } elseif (extension_loaded('gd')) {
            return $this->createFromGd($watermark, $asset, $newImagePath, $position, $padding, $quality, $format, $model, $filename);
        } else {
            throw new InvalidConfigException('GD or Imagick extension is required.');
        }

    }

    private function createFromImagix($watermark, $asset, $newImagePath, $position, $padding, $quality, $format, $model, $filename) {

        $watermark = new \Imagick($watermark);
        $asset = new \Imagick($asset);

        $asset->compositeImage(
            $watermark,
            Imagick::COMPOSITE_OVER,
            $this->getPositionX($asset->getImageWidth(), $watermark->getImageWidth(), $position, $padding),
            $this->getPositionY($asset->getImageHeight(), $watermark->getImageHeight(), $position, $padding)
        );
        $asset->setImageCompressionQuality($quality);
        $asset->setimageformat($format);

        $asset->writeImage($newImagePath);
        $asset->clear();

        return $model->getDirectory() . $filename;
    }

    private function createFromGd($watermark, $asset, $newImagePath, $position, $padding, $quality, $format, $model, $filename) {

        $asset = imagecreatefromstring(file_get_contents($asset));
        $watermark = imagecreatefromstring(file_get_contents($watermark));
        imagecopy(
            $asset,
            $watermark,
            $this->getPositionX(imagesx($asset), imagesx($watermark), $position, $padding),
            $this->getPositionY(imagesy($asset), imagesy($watermark), $position, $padding),
            0,
            0,
            imagesx($watermark),
            imagesy($watermark)
        );

        if ($format == 'jpg') {
            imagejpeg($asset, $newImagePath, $quality);
        } elseif ($format == 'png') {
            $quality = $quality / 10;
            if ($quality == 10)  {
                $quality = 9;
            }
            imagepng($asset, $newImagePath, $quality);
        } elseif ($format == 'gif') {
            imagegif($asset, $newImagePath);
        } elseif ($format == 'webp') {
            imagewebp($asset, $newImagePath, $quality);
        }

        return $model->getDirectory() . $filename;
    }

    /**
     * @param $image
     * @param $watermark
     * @param $position
     * @param int $padding
     * @return int
     */
    public function getPositionX($imageWidth, $watermarkWidth, $position, int $padding): int
    {
        return match ($position) {
            'top-right', 'bottom-right', 'center-right' => $imageWidth - $watermarkWidth - $padding,
            'top-center', 'bottom-center', 'center-center' => ($imageWidth - $watermarkWidth) / 2 + $padding,
            default => 0 + $padding
        };
    }

    /**
     * @param $image
     * @param $watermark
     * @param $position
     * @param int $padding
     * @return int
     */
    public function getPositionY($imageHeight, $watermarkHeight, $position, int $padding): int
    {
        return match ($position) {
            'bottom-left', 'bottom-center', 'bottom-right' => $imageHeight - $watermarkHeight - $padding,
            'center-center' => ($imageHeight - $watermarkHeight) / 2,
            'center-left', 'center-right' => ($imageHeight - $watermarkHeight) / 2 - $padding,
            default => 0 + $padding
        };
    }

}
