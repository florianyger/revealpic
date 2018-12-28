<?php

namespace App\Utils;

use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

trait PictureTrait
{
    /**
     * @param string $slug
     * @param string $filename
     *
     * @return BinaryFileResponse
     */
    public function renderPicture($slug, $filename)
    {
        $response = new BinaryFileResponse($this->getPiecePicturePath($slug, $filename));
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $filename,
            iconv('UTF-8', 'ASCII//TRANSLIT', $filename)
        );

        return $response;
    }

    /**
     * @return string
     */
    public function getPageDirectoryPath()
    {
        // Case of services with DI
        if (isset($this->picturesDirectoryPath)) {
            return $this->picturesDirectoryPath;
        }

        // Case of controllers
        if (isset($this->container)) {
            return $this->container->getParameter('app.pictures_directory_path');
        }

        throw new ParameterNotFoundException('PageDirectoryPath');
    }

    /**
     * @param string $slug
     *
     * @return string
     */
    public function getPagePicturesDirectoryPath($slug)
    {
        return join('/', [$this->getPageDirectoryPath(), $slug]);
    }

    /**
     * @param string $slug
     * @param string $filename
     *
     * @return string
     */
    public function getPiecePicturePath($slug, $filename)
    {
        return join('/', [$this->getPagePicturesDirectoryPath($slug), $filename]);
    }
}
