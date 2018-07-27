<?php

namespace App\Service;

use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\Finder\Finder;

class PictureService
{
    const NB_PIECE = 50;
    const PIECE_PREFIX = '_piece';

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
                $image
                    ->crop($pieceSide, $pieceSide, $i, $j)
                    ->save($pictureDirectoryPath . '/' . self::PIECE_PREFIX . '-' . $i . '-' . $j . '.jpg')
                ;

                $image->reset();
            }
        }
    }

    /**
     * @param string $slug
     *
     * @return array
     */
    public function getPieces($slug)
    {
        $finder = new Finder();
        $finder->files()->in($this->picturesDirectoryPath . '/' . $slug);

        $pieces = [];
        foreach ($finder as $file) {
            if (false === strpos($file->getFilename(), self::PIECE_PREFIX)) {
                continue;
            }

            $fileName = str_replace(self::PIECE_PREFIX . '-', '', $file->getFilename());
            $fileName = preg_replace('/\.[A-Za-z1-9]*/', '', $fileName);
            list($top, $left) = explode('-', $fileName);

            $pieces[] = [
                'top' => $top,
                'left' => $left,
                'slug' => $slug,
                'name' => $file->getFilename()
            ];
        }

        return $pieces;
    }

    /**
     * @param string $slug
     * @param $name
     *
     * @return string
     */
    public function getPiecePath($slug, $name)
    {
        return $this->picturesDirectoryPath . '/' . $slug . '/' . $name;
    }
}