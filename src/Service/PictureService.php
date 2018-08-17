<?php

namespace App\Service;

use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\Finder\Finder;

class PictureService
{
    const NB_PIECE = 50;
    const PIECE_EXTENSION = '.jpg';

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
        $pictureDirectoryPath = join('/', [$this->picturesDirectoryPath, $slug]);

        $image = Image::make(
            join('/', [$pictureDirectoryPath, $pictureName])
        );

        $width = $image->getWidth();
        $height = $image->getHeight();
        $pieceSide = ceil(
            sqrt(
                ($width * $height) / self::NB_PIECE
            )
        );

        $image->backup();
        for ($i = 0; $i < $width; $i += $pieceSide) {
            for ($j = 0; $j < $height; $j += $pieceSide) {
                $imageName = join('-', [$i, $j . self::PIECE_EXTENSION]);

                $image
                    ->crop($pieceSide, $pieceSide, $i, $j)
                    ->save(join('/', [$pictureDirectoryPath, $imageName]))
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
        $finder->files()->in(
            join('/', [$this->picturesDirectoryPath, $slug])
        );

        $pieces = [];
        foreach ($finder as $file) {
            if (false !== strpos($file->getFilename(), $slug)) {
                continue;
            }

            $fileName = preg_replace('/\.\w*/', '', $file->getFilename());
            list($left, $top) = explode('-', $fileName);

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
        return join('/', [$this->picturesDirectoryPath, $slug, $name]);
    }
}