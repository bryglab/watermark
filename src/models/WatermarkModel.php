<?php
namespace stefanladner\craftwatermark\models;

use Craft;
use craft\base\Model;
use Imagine\Imagick\Imagick;
use stefanladner\craftwatermark\Watermark;
use yii\base\InvalidConfigException;

class WatermarkModel extends Model
{

    public array $watermark = [];
    public string $directory = '';

    public function getAbsoluteDirectory(): string
    {
        return Craft::getAlias('@webroot') . DIRECTORY_SEPARATOR . Watermark::getInstance()->getSettings()->directory . DIRECTORY_SEPARATOR;
    }

    public function getDirectory(): string
    {
        return DIRECTORY_SEPARATOR . Watermark::getInstance()->getSettings()->directory . DIRECTORY_SEPARATOR;
    }

    public function getAssetPath(): string
    {
        // TODO: Implement getAssetPath() method.
    }

    public function getFormat(): string
    {
        return Watermark::getInstance()->getSettings()->format;
    }

    /**
     * @throws \ImagickException
     * @throws InvalidConfigException
     */
    public function getWatermark(): \Imagick
    {
        $watermarkId        = Watermark::getInstance()->getSettings()->imageId[0];
        $watermarkAsset     = Craft::$app->assets->getAssetById($watermarkId);
        $watermarkFsPath    = Craft::getAlias($watermarkAsset->getVolume()->fs->path);
        $watermark          = $watermarkFsPath . DIRECTORY_SEPARATOR . $watermarkAsset->getPath();
        $watermark = new \Imagick($watermark);
        $watermark->resizeImage(
            Watermark::getInstance()->getSettings()->watermarkWidth,
            Watermark::getInstance()->getSettings()->watermarkHeight,
            Imagick::FILTER_LANCZOS,
            1,
            Watermark::getInstance()->getSettings()->bestFit

        );
        return $watermark;
    }

    public function getWatermarkedImage($assetId): string
    {
        $directory = $this->getDirectory();
        return $directory . md5($assetId) . '.' . $this->getFormat();
    }

    public function createWatermark($image, $options): string
    {
        // Create the watermarked image
        $directory = $this->getAbsoluteDirectory();
        $filename = md5($image->id) . '.' . $this->getFormat();

        $watermarkAsset     = Craft::$app->assets->getAssetById($image->id);
        $watermarkFsPath    = Craft::getAlias($watermarkAsset->getVolume()->fs->path);
        $watermark          = $watermarkFsPath . DIRECTORY_SEPARATOR . $watermarkAsset->getPath();

        // TODO Implement sub folder structure to identify the image

        $imagick = new \Imagick($watermark);
        $imagick->compositeImage($this->getWatermark(), Imagick::COMPOSITE_OVER, 0, 0);
        $newImagePath = $directory . md5($image->id) . '.' . $this->getFormat();;
        $imagick->writeImage($newImagePath);
        $imagick->clear();

        return $this->getDirectory() . $filename;
    }

    public function exists($assetId): bool
    {
        $directory = $this->getAbsoluteDirectory();
        $path = $directory . md5($assetId) . '.' . $this->getFormat();;
        return file_exists($path);
    }

    public function doWatermarkTransform()
    {
        // TODO: Implement doWatermarkTransform() method.
    }


}