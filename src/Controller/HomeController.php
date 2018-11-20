<?php

namespace App\Controller;

use App\Entity\Page;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $pageRepository = $this->getDoctrine()->getManager()->getRepository(Page::class);

        /** @var Page[] $lastPages */
        $lastPages = $pageRepository->findBy([], ['updatedAt' => 'desc'], 10);

        return $this->render('home/index.html.twig', [
            'lastPages' => $lastPages,
        ]);
    }
}
