<?php
namespace stefanladner\craftwatermark\models;

use Craft;
use craft\base\Model;

class WatermarkModel extends Model
{

    public array $watermark = [];

    public function getWatermarkedImage($assetId): string
    {
        return '/watermark/' . md5($assetId) . '.jpg';
    }

    public function createWatermark($image, $options)
    {
        // Create the watermarked image
        return "test";
    }

    public function exists(): bool
    {
        return file_exists('/Users/stefanladner/htdocs/plugins.local/web/test.jpg');
    }

    public function waterMarkDoTransform()
    {

    }


}