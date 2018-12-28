<?php

namespace App\Service;

use App\Entity\Page;
use App\Entity\Piece;
use App\Utils\PictureTrait;
use Intervention\Image\Constraint;
use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic as ImageManager;

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
    public function cutPagePictureInPieces(Page $page): array
    {
        $picture = $this->createSizedPicture($page->getSlug(), $page->getPictureName());

        $pictureWidth = $picture->getWidth();
        $pictureHeight = $picture->getHeight();
        $pieceWidth = ceil($pictureWidth / self::NB_COLUMN);
        $pieceHeight = ceil($pictureHeight / self::NB_LINE);

        $picture->backup();
        $pieces = [];
        for ($i = 0; $i < self::NB_COLUMN; $i++) {
            $leftPos = $i * $pieceWidth;

            for ($j = 0; $j < self::NB_LINE; $j++) {
                $topPos = $j * $pieceHeight;

                $pictureName = join('-', [$leftPos, $topPos . self::PIECE_EXTENSION]);

                $width = min($pieceWidth, $pictureWidth - $leftPos);
                $height = min($pieceHeight, $pictureHeight - $topPos);

                $picture
                    ->crop($width, $height, $leftPos, $topPos)
                    ->save($this->getPiecePicturePath($page->getSlug(), $pictureName))
                ;

                $pieces[] = (new Piece())
                    ->setFilename($pictureName)
                    ->setPage($page)
                    ->setWidth($width)
                    ->setHeight($height)
                    ->setLeftPos($leftPos)
                    ->setTopPos($topPos)
                ;

                $picture->reset();
            }
        }

        return $pieces;
    }

    /**
     * @param string $slug
     * @param string $filename
     *
     * @return Image
     */
    private function createSizedPicture($slug, $filename): Image
    {
        $noUpsize = function (Constraint $constraint) {
            $constraint->upsize();
        };

        return ImageManager::make(
            $this->getPiecePicturePath($slug, $filename)
        )
            ->widen(self::MAX_PICTURE_WIDTH, $noUpsize)
            ->heighten(self::MAX_PICTURE_HEIGHT, $noUpsize)
        ;
    }
}
