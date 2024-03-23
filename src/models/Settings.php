<?php

namespace stefanladner\craftwatermark\models;

use Craft;
use craft\base\Model;

/**
 * Watermark settings
 */
class Settings extends Model
{
    public string $test = '';

    public array $imageId = [];

    public function rules(): array
    {
        return [
            [['test'], 'required']
        ];
    }
}
