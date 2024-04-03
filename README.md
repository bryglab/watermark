# Watermark Plugin for Craft CMS

A Craft CMS Plugin to add watermarks to images.\
Whether it's a social image or an inline image in your project, you can add a watermark image to it.

Works with both Imagick and GD.

## Usage

Pretty simple, just use the `watermark` filter in your templates, which will return a ***full url (!)*** to the watermarked image.

```twig
{% set image = entry.image.one() %}
<img src="{{ image | watermark }}" ...>
```


The `watermark` filter accepts optional parameters to override the general settings just for your use case:


```twig
{% set options = {
    transform: 'handle/array',
    asset: 'override settings with an asset object',
    position: 'top-left|top-center|top-right|center-left|center-center|center-right|bottom-left|bottom-center|bottom-right',
    padding: 10,
    width: 100,
    height: 100,
    quality: 75,
    format: 'jpg|png|gif|webp',
    mode: 'fit|stretch'
} %}
{% set image = entry.image.one() | watermark(options) %}
```

## Settings

Please check the plugin settings page in the Craft Control Panel.
There are some important settings you should configure before using the filter.

* **Directory**: The directory where the watermarked images are stored.
* **Watermark Image**: The image to use as a watermark.
* **Position**: The position of the watermark on the image.
* **Padding**: The padding around the watermark.
* **Width**: The width of the watermark.
* **Height**: The height of the watermark.
* **Quality**: The quality of the output image.
* **Format**: The format of the output image.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “Watermark”. Then press “Install”.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require bryglab/watermark

# tell Craft to install the plugin
./craft plugin/install watermark
```
## Requirements

This plugin requires Craft CMS 5.0.0 or later, and PHP 8.0.2 or later.
