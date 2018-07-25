<?php

namespace App\Controller;

use App\Entity\Page;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index()
    {
        $pageRepository = $this->getDoctrine()->getManager()->getRepository(Page::class);

        /** @var Page[] $lastPages */
        $lastPages = $pageRepository->findBy([], ['updatedAt' => 'desc'], 10);

        return $this->render('dashboard/index.html.twig', [
            'lastPages' => $lastPages,
        ]);
    }
}
