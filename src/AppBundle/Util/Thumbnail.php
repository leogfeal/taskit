<?php

/*
 * (c) Daymer Rodriguez Fillad <daymerfillad.gmail.com>
 *
 * Thumbnail is a class wrote by Daymer Rodriguez Fillad to 
 * automatize the process of generate preview for pictures 
 * from site www.hostalescuba.com
 */

namespace AppBundle\Util;

class Thumbnail {

    private $image, $type, $width, $height;

    public function loadImage($pathImage) {
        $info = getimagesize($pathImage);
        $this->width = $info[0];
        $this->height = $info[1];
        $this->type = $info[2];

        switch ($this->type) {
            case IMAGETYPE_JPEG:
                $this->image = imagecreatefromjpeg($pathImage);
                break;
            case IMAGETYPE_PNG:
                $this->image = imagecreatefrompng($pathImage);
                break;
            case IMAGETYPE_GIF:
                $this->image = imagecreatefromgif($pathImage);
                break;
        }
    }

    public function save($path, $quality = 100) {
        switch ($this->type) {
            case IMAGETYPE_JPEG:
                imagejpeg($this->image, $path, $quality);
                break;
            case IMAGETYPE_PNG:
                $pngquality = floor(($quality - 10) / 10);
                imagepng($this->image, $path, $pngquality);
                break;
            case IMAGETYPE_GIF:
                imagegif($this->image, $path);
                break;
        }
    }

    public function resize($prop, $value) {
        $prop_valor = ($prop == 'width') ? $this->width : $this->height;
        $pro_versus = ($prop == 'width') ? $this->height : $this->width;

        //Calc value from opposed position
        $proporc = $value / $prop_valor;
        $value_versus = $pro_versus * $proporc;

        $image = ($prop == 'width') ? imagecreatetruecolor($value, $value_versus) :
                imagecreatetruecolor($value_versus, $value);

        //remove transparency        
        if ($this->type == IMAGETYPE_PNG) {
            $white = imagecolorallocate($image, 255, 255, 255);
            if ($prop == 'width')
                imagefilledrectangle($image, 0, 0, $value, $value_versus, $white);
            else
                imagefilledrectangle($image, 0, 0, $value_versus, $value, $white);
        }

        if ($prop == 'width')
            imagecopyresampled($image, $this->image, 0, 0, 0, 0, $value, $value_versus, $this->width, $this->height);
        else
            imagecopyresampled($image, $this->image, 0, 0, 0, 0, $value_versus, $value, $this->width, $this->height);

        $this->image = $image;
    }

    public function resizeExact($width, $height) {
        $image = imagecreatetruecolor($width, $height);
        imagecopyresampled($image, $this->image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
        $this->image = $image;
    }

    public function getWidth() {
        return $this->width;
    }

    public function getHeight() {
        return $this->height;
    }

}
