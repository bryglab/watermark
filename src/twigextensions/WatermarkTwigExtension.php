<?php

namespace stefanladner\craftwatermark\twigextensions;

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

    public function watermark($image, $options): false|string
    {
        $watermark = new WatermarkModel();
        if ($watermark->exists()) {
            // Return the watermarked image
            return $watermark->getWatermarkedImage($image->id);
        } else {
            // Create the watermarked image
            return $watermark->createWatermark($image, $options);
        }
    }
}