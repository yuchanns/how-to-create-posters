<?php
require_once 'vendor/autoload.php';

use Intervention\Image\ImageManager;

$manager = new ImageManager(['driver' => 'gd']);

foreach (range(1, 8) as $number) {
    $path = 'image/' . $number . '.jpeg';
    $image = $manager->make($path);
    $image->resize(1024, null, function ($constraint) {
        /**
         * @var \Intervention\Image\Constraint $constraint
         */
        $constraint->aspectRatio();
    });
    $image->blur(5);
    // 保存为渐进式jpeg
    $image->interlace(true);
    $image->save($path, 80);
    echo $number . '.jpeg is done' . PHP_EOL;
}