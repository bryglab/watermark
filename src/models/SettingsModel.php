<?php

namespace bryglab\watermark\models;

use craft\base\Model;

/**
 * Class SettingsModel
 * @package bryglab\watermark\models
 */
class SettingsModel extends Model
{

    public string $directory;
    public string $format;
    public string $position;
    public array $watermarkImage;
    public int $watermarkWidth;
    public int $watermarkHeight;
    public int $quality;
    public int $padding;
    public bool $bestFit;

    public function rules(): array
    {
        return [
            // TODO: Check, width and height should be required
            [['directory', 'watermarkImage', 'watermarkWidth', 'watermarkHeight'], 'required']
        ];
    }

}
