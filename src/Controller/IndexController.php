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
            'title' => 'IndexController',
        ]);
    }

    #[Route('/services', name: 'services')]
    public function services(): Response
    {
        return $this->render('index/services.html.twig', [
            'title' => 'SERVICES',
            'items' => '10',
        ]);
    }

    #[Route('/services/item/{id<\d+>}', name: 'servicesItem')]
    public function servicesItem(int $id): Response
    {
        return $this->render('index/servicesItem.html.twig', [
            'title' => 'SERVICES ITEM' . $id,
            'description' => 'description',
            'price' => '1000'
        ]);
    }

    #[Route('/transactions', name: 'transactions')]
    public function transactions(): Response
    {
        return $this->render('index/transactions.html.twig', [
            'title' => 'Ваши транзакции',
            'items' => '10'
        ]);
    }
}
