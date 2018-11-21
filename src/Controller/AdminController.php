<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route("/show/{slug}/{filename}", name="show_image")
     */
    public function showAction($slug, $filename)
    {
        $filePath = join('/', [$this->container->getParameter('app.pictures_directory_path'), $slug, $filename]);

        if (!$this->getUser()->isAdmin()) {
            throw new AccessDeniedException($filePath);
        }

        $response = new BinaryFileResponse($filePath);
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $filename,
            iconv('UTF-8', 'ASCII//TRANSLIT', $filename)
        );

        return $response;
    }
}
