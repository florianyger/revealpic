<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Piece;
use App\Service\PictureService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/viewer")
 */
class ViewerController extends Controller
{
    /**
     * @Route("/{slug}", name="viewer")
     */
    public function index(Page $page)
    {
        if (!$page) {
            throw $this->createNotFoundException(
                'No page found for slug '.$page->getSlug()
            );
        }

        $page->setViewCount($page->getViewCount() + 1);

        $this->getDoctrine()->getManager()->flush();

        return $this->render('viewer/index.html.twig', [
            'page' => $page,
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route("/click/{piece}", name="click_on_piece", condition="request.isXmlHttpRequest()")
     */
    public function clickOnPiece(Piece $piece)
    {
        $piece->addClickToReveal();

        $imageUrl = null;
        if ($piece->isRevealed()) {
            $imageUrl = $this->generateUrl('show_piece', ['piece' => $piece->getId()]);
        }

        $this->getDoctrine()->getManager()->flush();

        return new JSONResponse(
            [
                'piece' => json_encode(
                    [
                        'id' => $piece->getId(),
                        'filename' => $piece->getFilename(),
                        'imageUrl' => $imageUrl,
                        'nbClickToReveal' => $piece->getNbClickToReveal(),
                        'revealed' => $piece->isRevealed(),
                    ]
                ),
            ]
        );
    }

    /**
     * @Method({"GET"})
     * @Route("/show/{piece}", name="show_piece")
     */
    public function showAction(Piece $piece, PictureService $pictureService)
    {
        if (!$piece->isRevealed()) {
            throw new AccessDeniedException($piece->getId());
        }

        $response = new BinaryFileResponse($pictureService->getPiecePath($piece));
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $piece->getFilename(),
            iconv('UTF-8', 'ASCII//TRANSLIT', $piece->getFilename())
        );

        return $response;
    }
}
