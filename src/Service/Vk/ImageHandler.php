<?php


namespace App\Service\Vk;


class ImageHandler
{
    protected const REQUIRED_WIDTH = 400;
    protected const REQUIRED_HEIGHT = 400;
    protected const BORDER_COLOR = '#f7f9fa';

    protected $path;
    protected $filePath;

    public function __construct(string $path)
    {
        $this->path = $path;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    public function prepareImage(string $path)
    {
        $image = new \Imagick($path);
        $borderHeight = self::REQUIRED_HEIGHT - $image->getImageHeight();
        $borderHeight = $borderHeight > 0 ? $borderHeight : 0;
        $borderWidth = self::REQUIRED_WIDTH - $image->getImageWidth();
        $borderWidth = $borderWidth > 0 ? $borderWidth : 0;
        if ($borderHeight > 0 || $borderWidth > 0) {
            $image->borderImage(new \ImagickPixel(self::BORDER_COLOR), $borderWidth, $borderHeight);
        }
        $fileName = md5($image) . '_' . time() . '.jpeg';
        $this->filePath = $this->path . '/' . $fileName;
        $image->writeImage($this->filePath);

        return $this->filePath;
    }

    public function clear()
    {
        try {
            if ($this->filePath) {
                unlink($this->filePath);
            }
        } catch (\Exception $e) {
        }
    }
}