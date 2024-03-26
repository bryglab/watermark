<?php

namespace bryglab\watermark\models;

use Craft;
use craft\base\Model;
use Imagine\Imagick\Imagick;
use bryglab\watermark\Watermark;
use bryglab\watermark\services\WatermarkService;

/**
 * Class WatermarkModel
 * @package bryglab\watermark\models
 */
class WatermarkModel extends Model
{

    // Public Properties
    public array $watermark = [];
    public string $directory = 'watermark';
    public string $format = 'jpg';
    public int $quality = 100;
    public int $watermarkWidth = 100;
    public int $watermarkHeight = 100;
    public bool $bestFit = true;
    public string $position = 'top-left';
    public array $watermarkImage = [];


    /**
     * @return string
     */
    public function getAbsoluteDirectory(): string
    {
        return Craft::getAlias('@webroot') . DIRECTORY_SEPARATOR . Watermark::getInstance()->getSettings()->directory . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return DIRECTORY_SEPARATOR . Watermark::getInstance()->getSettings()->directory . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return Watermark::getInstance()->getSettings()->format;
    }

    /**
     * @param $assetId
     * @return bool
     */
    public function exists($assetId): bool
    {
        $directory = $this->getAbsoluteDirectory();
        $path = $directory . md5($assetId) . '.' . $this->getFormat();
        return file_exists($path);
    }

    /**
     * @return \Imagick
     * @throws \ImagickException|\yii\base\InvalidConfigException
     */
    public function getWatermark(): \Imagick
    {
        $watermarkId = Watermark::getInstance()->getSettings()->watermarkImage[0];
        $watermarkAsset = Craft::$app->assets->getAssetById($watermarkId);
        $watermarkFsPath = Craft::getAlias($watermarkAsset->getVolume()->fs->path);
        $watermark = $watermarkFsPath . DIRECTORY_SEPARATOR . $watermarkAsset->getPath();
        $watermark = new \Imagick($watermark);
        $watermark->resizeImage(Watermark::getInstance()->getSettings()->watermarkWidth, Watermark::getInstance()->getSettings()->watermarkHeight, Imagick::FILTER_LANCZOS, 1, Watermark::getInstance()->getSettings()->bestFit);
        return $watermark;
    }

    /**
     * @param $assetId
     * @return string
     */
    public function getWatermarkedImage($assetId): string
    {
        $directory = $this->getDirectory();
        return $directory . md5($assetId) . '.' . $this->getFormat();
    }

    /**
     * @param $image
     * @param $options
     * @return string
     * @throws \ImagickException
     */
    public function createWatermark($image, $options): string
    {
        // Create the watermarked image
        $directory = $this->getAbsoluteDirectory();
        $filename = md5($image->id) . '.' . $this->getFormat();

        $watermarkAsset = Craft::$app->assets->getAssetById($image->id);
        $watermarkFsPath = Craft::getAlias($watermarkAsset->getVolume()->fs->path);
        $watermark = $watermarkFsPath . DIRECTORY_SEPARATOR . $watermarkAsset->getPath();

        $newImagePath = $directory . md5($image->id) . '.' . $this->getFormat();

        $imagick = new \Imagick($watermark);
        $imagick->compositeImage($this->getWatermark(), Imagick::COMPOSITE_OVER, $this->getPositionX($imagick, $this->getWatermark(), Watermark::getInstance()->getSettings()->position), $this->getPositionY($imagick, $this->getWatermark(), Watermark::getInstance()->getSettings()->position),);
        $imagick->setImageCompressionQuality(Watermark::getInstance()->getSettings()->quality);
        $imagick->setimageformat($this->getFormat());
        $imagick->writeImage($newImagePath);
        $imagick->clear();

        return $this->getDirectory() . $filename;
    }

    /**
     * @param $image
     * @param $watermark
     * @param $position
     * @return int
     */
    public function getPositionX($image, $watermark, $position): int
    {
        $imageWidth = $image->getImageWidth();
        $watermarkWidth = $watermark->getImageWidth();

        return match ($position) {
            'top-right', 'bottom-right', 'center-right' => $imageWidth - $watermarkWidth,
            'top-center', 'bottom-center', 'center-center' => ($imageWidth - $watermarkWidth) / 2,
            default => 0,
        };
    }

    /**
     * @param $image
     * @param $watermark
     * @param $position
     * @return int
     */
    public function getPositionY($image, $watermark, $position): int
    {
        $imageHeight = $image->getImageHeight();
        $watermarkHeight = $watermark->getImageHeight();

        return match ($position) {
            'bottom-left', 'bottom-center', 'bottom-right' => $imageHeight - $watermarkHeight,
            'center-left', 'center-center', 'center-right' => ($imageHeight - $watermarkHeight) / 2,
            default => 0,
        };
    }


    public function doWatermarkTransform()
    {
        // TODO: Implement doWatermarkTransform() method.
    }


}