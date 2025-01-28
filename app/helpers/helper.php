<?php

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Geometry\Factories\RectangleFactory;

if (!function_exists('nidCensoredImage')) {
    function nidCensoredImage($image, $type, $number)
    {
        $manager = new ImageManager(new Driver());
        $img = $manager->read($image->path());

        $width = $img->width();
        $height = $img->height();

        if ($type === 'nid') {
            $leftOffset = intval($width * 0.09);
            $bottomOffset = intval($height * 0.05);

            $x = intval($width * 0.5) - $leftOffset;
            $y = intval($height * 0.8) + $bottomOffset;
            $rectWidth = intval($width * 0.45);
            $rectHeight = intval($height * 0.1);

            $img->drawRectangle($x, $y, function (RectangleFactory $rectangle) use ($rectWidth, $rectHeight) {
                $rectangle->size($rectWidth, $rectHeight);
                $rectangle->background('#000000');
            });
        } else {
            $topMargin = intval($height * 0.1);

            $x1 = intval($width * 0.6);
            $y1 = intval($height * 0.3) + $topMargin;
            $rectWidth1 = intval($width * 0.3);
            $rectHeight1 = intval($height * 0.3);

            $img->drawRectangle($x1, $y1, function (RectangleFactory $rectangle) use ($rectWidth1, $rectHeight1) {
                $rectangle->size($rectWidth1, $rectHeight1);
                $rectangle->background('#000000');
            });

            $x2 = 0;
            $y2 = intval($height * 0.8);
            $rectWidth2 = $width;
            $rectHeight2 = intval($height * 0.2);

            $img->drawRectangle($x2, $y2, function (RectangleFactory $rectangle) use ($rectWidth2, $rectHeight2) {
                $rectangle->size($rectWidth2, $rectHeight2);
                $rectangle->background('#000000');
            });
        }

        $censoredImagePath = 'uploads/censored/' . time() . '_censored.jpg';

        $img->encodeByPath(public_path($censoredImagePath), progressive: true, quality: 100);
        $img->save(public_path($censoredImagePath));

        return $censoredImagePath;
    }
}
