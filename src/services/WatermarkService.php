<?php

namespace bryglab\watermark\services;

use Craft;
use craft\base\Component;
use yii\base\InvalidConfigException;

/**
 * Watermark service
 * @package bryglab\watermark\services
 */
class WatermarkService extends Component
{

    /**
     * Apply watermark to the asset
     *
     * @param int $assetId
     * @throws \ImagickException|InvalidConfigException
     */
    public function applyWatermark($assetId): void
    {
        $watermarkModel = new WatermarkModel();
        $watermark = $watermarkModel->getWatermark();
        $asset = Craft::$app->assets->getAssetById($assetId);
        $assetFsPath = Craft::getAlias($asset->getVolume()->fs->path);
        $image = $assetFsPath . DIRECTORY_SEPARATOR . $asset->getPath();
        $image = new \Imagick($image);
        $image->compositeImage($watermark, \Imagick::COMPOSITE_OVER, 0, 0);
        $image->writeImage($watermarkModel->getAbsoluteDirectory() . md5($assetId) . '.' . $watermarkModel->getFormat());
    }


}
