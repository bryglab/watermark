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
        $watermarkId = $options['asset']->id ?? Watermark::getInstance()->getSettings()->watermarkImage[0];
        $watermarkWidth = $options['width'] ?? Watermark::getInstance()->getSettings()->watermarkWidth;
        $watermarkHeight = $options['height'] ?? Watermark::getInstance()->getSettings()->watermarkHeight;
        $quality = $options['quality'] ?? Watermark::getInstance()->getSettings()->quality;
        $position = $options['position'] ?? Watermark::getInstance()->getSettings()->position;
        $format = $options['format'] ?? Watermark::getInstance()->getSettings()->format;
        $padding = $options['padding'] ?? Watermark::getInstance()->getSettings()->padding;

        $directory = $model->getAbsoluteDirectory();
        $filename = md5($image->id) . '.' . $format;

        // get watermark asset
        $watermarkAsset = Craft::$app->assets->getAssetById($watermarkId);
        $watermarkFsPath = Craft::getAlias($watermarkAsset->getVolume()->fs->path);
        $watermark = $watermarkFsPath . DIRECTORY_SEPARATOR . $watermarkAsset->getPath();
        $watermark = new \Imagick($watermark);
        $watermark->resizeImage($watermarkWidth, $watermarkHeight, Imagick::FILTER_LANCZOS, 1, Watermark::getInstance()->getSettings()->bestFit);

        // define paths
        if (isset($options['transform'])) {
            $watermarkPath = $image->getUrl($options['transform'], true);
            $watermarkPath = str_replace(Craft::$app->sites->getCurrentSite()->baseUrl, '', $watermarkPath);
            $watermarkPath = Craft::getAlias('@webroot') . DIRECTORY_SEPARATOR . $watermarkPath;
        } else {
            $watermarkPath = Craft::getAlias($image->getVolume()->fs->path);
            $watermarkPath = $watermarkPath . DIRECTORY_SEPARATOR . $image->getPath();
        }

        $newImagePath = $directory . md5($image->id) . '.' . $format;
        $imagick = new \Imagick($watermarkPath);
        $imagick->compositeImage(
            $watermark,
            Imagick::COMPOSITE_OVER,
            $this->getPositionX($imagick, $watermark, $position, $padding),
            $this->getPositionY($imagick, $watermark, $position, $padding)
        );
        $imagick->setImageCompressionQuality($quality);
        $imagick->setimageformat($format);
        // check if directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
        $imagick->writeImage($newImagePath);
        $imagick->clear();

        return $model->getDirectory() . $filename;
    }

    /**
     * @param $image
     * @param $watermark
     * @param $position
     * @return int
     */
    public function getPositionX($image, $watermark, $position, $padding = 0): int
    {
        $imageWidth = $image->getImageWidth();
        $watermarkWidth = $watermark->getImageWidth();

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
     * @return int
     */
    public function getPositionY($image, $watermark, $position, $padding = 0): int
    {
        $imageHeight = $image->getImageHeight();
        $watermarkHeight = $watermark->getImageHeight();

        return match ($position) {
            'bottom-left', 'bottom-center', 'bottom-right' => $imageHeight - $watermarkHeight - $padding,
            'center-center' => ($imageHeight - $watermarkHeight) / 2,
            'center-left', 'center-right' => ($imageHeight - $watermarkHeight) / 2 - $padding,
            default => 0 + $padding
        };
    }

    private function createFromImagix() {

    }

}
