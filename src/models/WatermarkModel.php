<?php

namespace bryglab\watermark\models;

use Craft;
use craft\base\Model;
use bryglab\watermark\Watermark;

/**
 * Class WatermarkModel
 * @package bryglab\watermark\models
 */
class WatermarkModel extends Model
{


    // Public Properties
    public array $watermark = [];
    public string $directory = 'watermark';
    public string $format = 'jpg';
    public int $quality = 100;
    public int $watermarkWidth = 100;
    public int $watermarkHeight = 100;
    public bool $bestFit = true;
    public string $position = 'top-left';
    public array $watermarkImage = [];

    /**
     * @return string
     */
    public function getAbsoluteDirectory(): string
    {
        return Craft::getAlias('@webroot') . DIRECTORY_SEPARATOR . Watermark::getInstance()->getSettings()->directory . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return DIRECTORY_SEPARATOR . Watermark::getInstance()->getSettings()->directory . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return Watermark::getInstance()->getSettings()->format;
    }

    /**
     * @param $assetId
     * @return bool
     */
    public function exists($assetId): bool
    {
        $directory = $this->getAbsoluteDirectory();
        $path = $directory . md5($assetId) . '.' . $this->getFormat();
        return file_exists($path);
    }

    /**
     * @param $assetId
     * @return string
     */
    public function getWatermarkedImage($assetId): string
    {
        $directory = $this->getDirectory();
        return $directory . md5($assetId) . '.' . $this->getFormat();
    }

}