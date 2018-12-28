<?php

namespace App\Service;

use App\Entity\Page;
use App\Entity\Piece;
use App\Utils\PictureTrait;
use Intervention\Image\Constraint;
use Intervention\Image\ImageManagerStatic as Image;

class PictureService
{
    use PictureTrait;

    const NB_COLUMN = 7;
    const NB_LINE = 7;
    const PIECE_EXTENSION = '.jpg';
    const MAX_PICTURE_WIDTH = 1000;
    const MAX_PICTURE_HEIGHT = 800;

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
     *
     * @return array
     */
    public function cutPictureInPieces($page): array
    {
        $image = Image::make(
            $this->getPiecePicturePath($page->getSlug(), $page->getImageName())
        )->widen(self::MAX_PICTURE_WIDTH, function (Constraint $constraint) {
            $constraint->upsize();
        })->heighten(self::MAX_PICTURE_HEIGHT, function (Constraint $constraint) {
            $constraint->upsize();
        });

        $imageWidth = $image->getWidth();
        $imageHeight = $image->getHeight();
        $pieceWidth = ceil($imageWidth / self::NB_COLUMN);
        $pieceHeight = ceil($imageHeight / self::NB_LINE);

        $image->backup();
        $pieces = [];
        for ($i = 0; $i < self::NB_COLUMN; $i++) {
            $leftPos = $i * $pieceWidth;

            for ($j = 0; $j < self::NB_LINE; $j++) {
                $topPos = $j * $pieceHeight;

                $imageName = join('-', [$leftPos, $topPos . self::PIECE_EXTENSION]);

                $width = min($pieceWidth, $imageWidth - $leftPos);
                $height = min($pieceHeight, $imageHeight - $topPos);

                $image
                    ->crop($width, $height, $leftPos, $topPos)
                    ->save($this->getPiecePicturePath($page->getSlug(), $imageName))
                ;

                $pieces[] = (new Piece())
                    ->setFilename($imageName)
                    ->setPage($page)
                    ->setWidth($width)
                    ->setHeight($height)
                    ->setLeftPos($leftPos)
                    ->setTopPos($topPos)
                ;

                $image->reset();
            }
        }

        return $pieces;
    }
}
