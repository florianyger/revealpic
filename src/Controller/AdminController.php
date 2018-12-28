<?php

namespace App\Controller;

use App\Utils\PictureTrait;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    use PictureTrait;

    /**
     * @Method({"GET"})
     * @Route("/show/{slug}/{filename}", name="show_image")
     *
     * @param string $slug
     * @param string $filename
     *
     * @return BinaryFileResponse
     */
    public function showAction($slug, $filename)
    {
        if (!$this->getUser()->isAdmin()) {
            throw new AccessDeniedException($slug);
        }

        return $this->renderPicture($slug, $filename);
    }
}
