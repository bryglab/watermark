<?php
namespace stefanladner\craftwatermark\models;

use Craft;
use craft\base\Model;
use Imagine\Imagick\Imagick;
use stefanladner\craftwatermark\Watermark;

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

    public function getWatermark(): \Imagick
    {
        $watermarkId        = Watermark::getInstance()->getSettings()->imageId[0];
        $watermarkAsset     = Craft::$app->assets->getAssetById($watermarkId);
        $watermarkFsPath    = Craft::getAlias($watermarkAsset->getVolume()->fs->path);
        $watermark          = $watermarkFsPath . DIRECTORY_SEPARATOR . $watermarkAsset->getPath();
        return new \Imagick($watermark);
    }

    public function getWatermarkedImage($assetId): string
    {
        $directory = $this->getDirectory();
        return $directory . md5($assetId) . '.jpg';
    }

    public function createWatermark($image, $options): string
    {
        // Create the watermarked image
        $directory = $this->getAbsoluteDirectory();
        $filename = md5($image->id) . '.jpg';

        $watermarkAsset     = Craft::$app->assets->getAssetById($image->id);
        $watermarkFsPath    = Craft::getAlias($watermarkAsset->getVolume()->fs->path);
        $watermark          = $watermarkFsPath . DIRECTORY_SEPARATOR . $watermarkAsset->getPath();

        // TODO Implement sub folder structure to identify the image

        $imagick = new \Imagick($watermark);
        $imagick->resizeImage(500, 300, Imagick::FILTER_LANCZOS, 1);
        $imagick->compositeImage($this->getWatermark(), Imagick::COMPOSITE_OVER, 15, 15);
        $newImagePath = $directory . md5($image->id) . '.jpg';
        $imagick->writeImage($newImagePath);
        $imagick->clear();

        return $this->getDirectory() . $filename;
    }

    public function exists($assetId): bool
    {
        $directory = $this->getAbsoluteDirectory();
        $path = $directory . md5($assetId) . '.jpg';
        return file_exists($path);
    }

    public function doWatermarkTransform()
    {
        // TODO: Implement doWatermarkTransform() method.
    }


}