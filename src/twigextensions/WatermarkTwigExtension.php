<?php

namespace stefanladner\craftwatermark\twigextensions;

use stefanladner\craftwatermark\Watermark;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use stefanladner\craftwatermark\models\WatermarkModel;

class WatermarkTwigExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('watermark', [$this, 'watermark']),
        ];
    }

    /**
     * @throws \Exception
     */
    public function watermark($image, $options = false): false|string
    {
        $watermark = new WatermarkModel();
        if ($watermark->exists($image->id)) {
            // Return the watermarked image
            return $watermark->getWatermarkedImage($image->id);

        } else {
            // Create the watermarked image
            if (!Watermark::getInstance()->getSettings()->imageId) {
                throw new \Exception("No watermark image found.\nDefine a watermark image in the plugin settings.");
            }
            return $watermark->createWatermark($image, $options);

        }
    }
}