<?php
namespace stefanladner\craftwatermark\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class WatermarkAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = "@stefanladner/craftwatermark/resources/";

        $this->depends = [
            CpAsset::class,
        ];

        $this->css = [
            'css/watermark.css',
        ];

        $this->js = [
            'js/watermark.js',
        ];

        parent::init();
    }
}