<?php

namespace bryglab\watermark\models;

use craft\base\Model;

/**
 * Watermark settings
 */
class SettingsModel extends Model
{

    public string $directory = 'watermarks';
    public string $format = 'jpg';
    public string $position = 'top-left';
    public array $imageId = [];
    public int $watermarkWidth = 100;
    public int $watermarkHeight = 100;
    public int $quality = 80;
    public bool $bestFit = true;

    public function rules(): array
    {
        return [
            // TODO: Check, width and height should be required
            [['directory', 'imageId', 'watermarkWidth', 'watermarkHeight'], 'required']
        ];
    }

}
