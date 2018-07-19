<?php

namespace App\Controller;

use App\Entity\Page;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
        $page = $repository->find($slug);

        if (!$page) {
            throw $this->createNotFoundException(
                'No page found for slug ' . $slug
            );
        }

        $page->setViewCount($page->getViewCount() + 1);

        $em->flush();

        return $this->render('viewer/index.html.twig', [
            'page' => $page,
        ]);
    }
}
