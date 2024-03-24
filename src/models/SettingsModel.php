<?php

namespace stefanladner\craftwatermark\models;

use craft\base\Model;

/**
 * Watermark settings
 */
class SettingsModel extends Model
{
    public string $directory    = '';
    public array $imageId       = [];

    public function rules(): array
    {
        return [
            [['directory', 'imageId'], 'required']
        ];
    }
}
