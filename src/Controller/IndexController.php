<?php

namespace App\Controller;

use App\Repository\ServicesRepository;
use App\Repository\BalanceRepository;
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

    public function getBalance(BalanceRepository $balanceRepository): Response
    {
        $balance = $balanceRepository->find(1)->getValue();
        return $this->render('base.html.twig', [
            'balance' => $balance
        ]);
    }

    #[Route('/services', name: 'services')]
    public function services(ServicesRepository $servicesRepository, BalanceRepository $balanceRepository): Response
    {
        $items = $servicesRepository->findAll();
        $balance = $balanceRepository->find(1)->getValue();
        return $this->render('index/services.html.twig', [
            'title' => 'Мои услуги',
            'items' => $items,
            'balance' => $balance
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
    public function transactions(BalanceRepository $balanceRepository): Response
    {
        $balance = $balanceRepository->find(1)->getValue();
        return $this->render('index/transactions.html.twig', [
            'title' => 'Ваши транзакции',
            'items' => '10',
            'balance' => $balance
        ]);
    }
}
