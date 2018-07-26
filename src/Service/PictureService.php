<?php

namespace App\Service;

use App\Entity\Page;
use Intervention\Image\ImageManagerStatic as Image;

class PictureService
{
    /**
     * @var string
     */
    private $picturesDirectoryPath;

    /**
     * @param string $picturesDirectoryPath
     */
    public function __construct(string $picturesDirectoryPath)
    {
        $this->picturesDirectoryPath = $picturesDirectoryPath;
    }

    /**
     * @param string $slug
     * @param string $pictureName
     */
    public function cutPictureInPieces($slug, $pictureName)
    {
        $pictureDirectoryPath = $this->picturesDirectoryPath . '/' . $slug;
        $picturePath = $pictureDirectoryPath . '/' . $pictureName;

        Image::make($picturePath);
    }
}