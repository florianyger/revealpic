<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

trait RenderImageTrait
{
    /**
     * @param string $slug
     * @param string $filename
     *
     * @return BinaryFileResponse
     */
    public function renderImage($slug, $filename)
    {
        $path = join('/', [$this->container->getParameter('app.pictures_directory_path'), $slug, $filename]);

        $response = new BinaryFileResponse($path);
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $filename,
            iconv('UTF-8', 'ASCII//TRANSLIT', $filename)
        );

        return $response;
    }
}
