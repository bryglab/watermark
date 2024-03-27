<?php

namespace bryglab\watermark\twigextensions;

use bryglab\watermark\Watermark;
use bryglab\watermark\models\WatermarkModel;
use bryglab\watermark\services\WatermarkService;
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
     * @param array $options
     * @return false|string
     * @throws \Exception
     */
    public function watermark($image, array $options = []): false|string
    {
        $model = new WatermarkModel();
        $service = new WatermarkService();
        if ($model->exists($image->id)) {
            // Return the watermarked image
            return $model->getWatermarkedImage($image->id);

        } else {
            // Create the watermarked image
            if (!Watermark::getInstance()->getSettings()->watermarkImage) {
                throw new \Exception("No watermark image found.\nDefine a watermark image in the plugin settings.");
            }
            return $service->createWatermark($image, $options);

        }
    }
}