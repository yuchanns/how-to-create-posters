<?php
namespace App\Controller;

require_once 'vendor/autoload.php';

use Intervention\Image\ImageManager;
use Endroid\QrCode\QrCode;

class Posters
{
    private $dir;

    public function __construct()
    {
        $this->dir = dirname(dirname(dirname(__FILE__)));
    }
    public function getPoster()
    {
        $relative_path = 'public/image/promotion/poster_yuchanns.jpg';
        if (!is_file($relative_path)) {
            $this->createPoster($relative_path);
        }
        return file_get_contents($this->dir . '/' . $relative_path);
    }

    /**
     * 在原背景图上插入名片
     *
     * @param string $path 图片路径
     * @return void
     */
    private function createPoster($path)
    {
        // 实例化图像处理类
        $manager = new ImageManager(['driver' => 'gd']);
        // 设置等比缩放匿名函数
        $constraint = function ($constraint) {
            /**
             * @var \Intervention\Image\Constraint $constraint
             */
            $constraint->aspectRatio();
        };
        // 处理水印
        $marker_path = 'public/image/marker/yuchanns.png';
        $marker = $manager->make($marker_path);
        $marker->resize(null, 43, $constraint);
        // 处理二维码
        $qrcode_path = 'public/image/qrcode/qrcode.png';
        $this->setQrcode($qrcode_path);
        $qr = $manager->make($qrcode_path);
        $qr->resize(70, null, $constraint);
        // 对缩放后的二维码进行锐化操作
        $qr->sharpen(15);
        // 处理名片信息（头像、名称、邀请码）
        $username = '羽毛';
        $invitation = '138*****223';
        $avatar_path = 'public/image/avatar/yuchanns.jpeg';
        $avatar = $manager->make($avatar_path);
        $avatar->resize(42, null, $constraint);
        // 创建一个遮罩，用于对头像进行处理
        $mask = $manager->canvas(42, 42);
        $mask->circle(42, 21, 21, function ($draw) {
            /**
             * @var \Intervention\Image\AbstractShape $draw
             */
            $draw->background('#fff');
        });
        // 对头像进行遮罩操作
        $avatar->mask($mask, false);
        // 设置字体匿名函数
        $font = function ($font) {
            /**
             * @var \Intervention\Image\AbstractFont $font
             */
            $font->file('public/image/font/pingfang.ttf');
            $font->size(14);
        };
        // 创建空白画布，作为名片背景
        $canvas = $manager->canvas(375, 120, '#fff');
        // $canvas->insert($poster, 'top-left');
        // $canvas->insert($marker, 'top-left', 20, 20);
        $canvas->insert($qr, 'top-left', 271, 12);
        $canvas->text('扫码加入', 275, 105, $font);
        $canvas->insert($avatar, 'top-left', 22, 22);
        $canvas->text($username, 72, 48, function ($font) {
            /**
             * @var \Intervention\Image\AbstractFont $font
             */
            $font->file('public/image/font/pingfang.ttf');
            $font->size(20);
        });
        $canvas->text('邀请您加入“工作室”', 22, 85, $font);
        $canvas->text('邀请码：' . $invitation, 22, 105, $font);
        // 处理海报
        $poster_path = 'public/image/upload/posters.jpeg';
        $poster = $manager->make($poster_path);
        $poster->resize(375, null, $constraint);
        // 在海报上插入水印
        $poster->insert($marker, 'top-left', 20, 20);
        // 在海报上插入名片
        $poster->insert($canvas, 'bottom-left', 0, 0);
        // 保存为渐进式jpeg
        $poster->interlace(true);
        $poster->save($path, 80);
    }

    private function setQrcode($path)
    {
        if (!is_file($path)) {
            // Create a basic QR code
            $qrCode = new QrCode('https://jq.qq.com/?_wv=1027&k=5do31Yv');
            $qrCode->setSize(300);

            // Set advanced options
            $qrCode->setWriterByName('png');
            $qrCode->setMargin(10);
            $qrCode->setEncoding('UTF-8');
            $qrCode->writeFile($path);
        }
    }
}
