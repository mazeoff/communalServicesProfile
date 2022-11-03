<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/index', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/services', name: 'services')]
    public function services(): Response
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'SERVICES',
        ]);
    }

    #[Route('/services/item/{id<\d+>}', name: 'servicesItem')]
    public function servicesItem(int $id): Response
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'SERVICES ITEM' . $id,
        ]);
    }
}
