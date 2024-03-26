<?php

namespace bryglab\watermark\twigextensions;

use bryglab\watermark\Watermark;
use bryglab\watermark\models\WatermarkModel;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class WatermarkTwigExtension
 * @package bryglab\watermark\twigextensions
 */
class WatermarkTwigExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('watermark', [$this, 'watermark']),
        ];
    }

    /**
     * @param $image
     * @param bool $options
     * @return false|string
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
            if (!Watermark::getInstance()->getSettings()->watermarkImage) {
                throw new \Exception("No watermark image found.\nDefine a watermark image in the plugin settings.");
            }
            return $watermark->createWatermark($image, $options);

        }
    }
}