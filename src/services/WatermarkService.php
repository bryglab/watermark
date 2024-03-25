<?php

namespace bryglab\watermark\services;

use Craft;
use craft\base\Component;

class WatermarkService extends Component
{

    public function applyWatermark($assetId)
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
