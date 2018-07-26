<?php

namespace App\Service;

use Intervention\Image\ImageManagerStatic as Image;

class PictureService
{
    const NB_PIECE = 50;

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

        $image = Image::make($picturePath);
        $image->backup();

        $width = $image->getWidth();
        $height = $image->getHeight();
        $area = $width * $height;
        $pieceArea = $area / self::NB_PIECE;
        $pieceSide = ceil(sqrt($pieceArea));

        for ($i = 0; $i < $width; $i += $pieceSide) {
            for ($j = 0; $j < $height; $j += $pieceSide) {
                $image->crop($pieceSide, $pieceSide, $i, $j)->save($pictureDirectoryPath . '/_piece-' . $i . '-' . $j . '.jpg');

                $image->reset();
            }
        }
    }
}