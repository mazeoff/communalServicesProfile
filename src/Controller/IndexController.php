<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\Transaction;
use App\Form\Type\TransactionType;
use App\Repository\ServicesRepository;
use App\Repository\BalanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
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
    public function services(Request $request,ServicesRepository $servicesRepository,
                             BalanceRepository $balanceRepository): Response

    {

        $items = $servicesRepository->findAll();
        $balance = $balanceRepository->find(1)->getValue();
        //считаем общую стоимость всех услуг за месяц
        $totalCostOfServices = 0;
        for ($i = 0; $i < count($items);$i++){
            if($items[$i]->isSubscription()){//если на услугу есть подписка
                $totalCostOfServices += $items[$i]->getPrice()*$items[$i]->getQuantity();//то считаем общую стоимость
            }

        }
        $totalCostOfServices *= 30;


        // create object
        $subscription = new Transaction();

        $form = $this->createForm(TransactionType::class, $subscription);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $transaction = $form->getData();

            // ... perform some action, such as saving the task to the database

            return $this->redirectToRoute('task_success');
        }

        return $this->render('index/services.html.twig', [
            'title' => 'Мои услуги',
            'items' => $items,
            'balance' => $balance,
            'totalCost' => $totalCostOfServices,
            'form' => $form->createView()
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
