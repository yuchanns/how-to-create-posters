<?php

require_once 'vendor/autoload.php';

use Intervention\Image\ImageManager;

function createThumbnail($img_url)
{
    $patharr = explode('/', $img_url);
    $filename = array_pop($patharr);
    $filenamearr = explode('.', $filename);
    $filenamearr[0] .= '_thumbnail';
    $filenamearr[1] = 'jpeg';
    $thumbnail_filename = join('.', $filenamearr);
    $patharr[] = $thumbnail_filename;
    $thumbnail_path = join('/', $patharr);
    echo $thumbnail_path . PHP_EOL;exit;
    // $domain = config('upload.domain');
    $manager = new ImageManager(['driver' => 'gd']);
    $poster = $manager->make(/*$domain . */$img_url);
    $poster->resize(null, 100, function ($constraint) {
        /**
         * @var \Intervention\Image\Constraint $constraint
         */
        $constraint->aspectRatio();
    });
    // 保存为渐进式jpeg
    $poster->interlace(true);
    $poster->save($thumbnail_path, 80);
    return true;
}

createThumbnail('/image/upload/posters6copy.png');