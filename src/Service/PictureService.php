<?php

namespace App\Service;

use App\Entity\Page;
use App\Entity\Piece;
use Intervention\Image\ImageManagerStatic as Image;

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
     * @param Page $page
     * @return array
     */
    public function cutPictureInPieces($page) : array
    {
        $pictureDirectoryPath = join('/', [$this->picturesDirectoryPath, $page->getSlug()]);

        $image = Image::make(
            join('/', [$pictureDirectoryPath, $page->getImageName()])
        );

        $width = $image->getWidth();
        $height = $image->getHeight();
        $pieceSide = ceil(
            sqrt(
                ($width * $height) / self::NB_PIECE
            )
        );

        $image->backup();
        $pieces = [];
        for ($i = 0; $i < $width; $i += $pieceSide) {
            for ($j = 0; $j < $height; $j += $pieceSide) {
                $imageName = join('-', [$i, $j . self::PIECE_EXTENSION]);

                $image
                    ->crop($pieceSide, $pieceSide, $i, $j)
                    ->save(join('/', [$pictureDirectoryPath, $imageName]))
                ;

                $pieces[] = (new Piece())
                    ->setFilename($imageName)
                    ->setPage($page)
                    ->setLeftPos($i)
                    ->setTopPos($j)
                ;

                $image->reset();
            }
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