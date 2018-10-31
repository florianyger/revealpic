<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Piece;
use App\Service\PictureService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ViewerController extends Controller
{
    /**
     * @Route("/viewer/{slug}", name="viewer")
     */
    public function index($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Page::class);

        /** @var Page $page */
        $page = $repository->findOneBy(
            ['slug' => $slug]
        );

        if (!$page) {
            throw $this->createNotFoundException(
                'No page found for slug ' . $slug
            );
        }

        $page->setViewCount($page->getViewCount() + 1);

        $em->flush();

        return $this->render('viewer/index.html.twig', [
            'page' => $page
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route("/click/{piece}", name="click_on_piece", condition="request.isXmlHttpRequest()")
     */
    public function clickOnPiece(Piece $piece)
    {
        $piece->addClickToReveal();

        $this->getDoctrine()->getManager()->flush();

        return new JSONResponse(
            [
                'piece' => json_encode(
                    [
                        'id' => $piece->getId(),
                        'nbClickToReveal' => $piece->getNbClickToReveal(),
                        'revealed' => $piece->getRevealed()
                    ]
                )
            ]
        );
    }

    /**
     * @param string $slug
     * @param string $name
     *
     * @return Response
     * @Method({"GET"})
     * @Route("/{slug}/{name}", name="image_show")
     *
     */
    public function showAction($slug, $name, PictureService $pictureService)
    {
        $response = new BinaryFileResponse($pictureService->getPiecePath($slug, $name));
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $name,
            iconv('UTF-8', 'ASCII//TRANSLIT', $name)
        );

        return $response;
    }
}
