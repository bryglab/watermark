<?php

namespace bryglab\watermark\models;

use craft\base\Model;

/**
 * Class SettingsModel
 * @package bryglab\watermark\models
 */
class SettingsModel extends Model
{

    // TODO: Check, if the properties should be public or protected
    public string $directory = 'watermarks';
    public string $format = 'jpg';
    public string $position = 'top-left';
    public array $watermarkImage = [];
    public int $watermarkWidth = 100;
    public int $watermarkHeight = 100;
    public int $quality = 80;
    public int $padding = 0;
    public bool $bestFit = true;

    public function rules(): array
    {
        return [
            // TODO: Check, width and height should be required
            [['directory', 'watermarkImage', 'watermarkWidth', 'watermarkHeight'], 'required']
        ];
    }

}
