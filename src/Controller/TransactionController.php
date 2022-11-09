<?php

namespace App\Controller;

use App\Controller\IndexController;
use App\Entity\Balance;
use App\Entity\Transaction;
use App\Form\Type\SubscriptionType;
use App\Form\Type\TopUpBalanceType;
use App\Repository\BalanceRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{

    #[Route('/transactions', name: 'transactions')]
    public function transactions(Request           $request,
                                  BalanceRepository $balanceRepository): Response
    {
        $balance = $balanceRepository->find(1)->getValue();

        $topUpBalanceTransaction = new Transaction();
        $topUpBalanceForm = $this->createForm(TopUpBalanceType::class, $topUpBalanceTransaction);

        $topUpBalanceForm->handleRequest($request);


        if ($topUpBalanceForm->isSubmitted() && $topUpBalanceForm->isValid()) {

            $balanceEntity = $balanceRepository->find(1);
            $currentBalance = $balanceEntity->getValue();
            $refill = $topUpBalanceForm->get('value')->getData();
            $currentBalance += $refill;
            $balanceEntity->setValue($currentBalance);
            $balanceRepository->save($balanceEntity, true);

            return $this->redirectToRoute('transactions');
        }
        return $this->render('index/transactions.html.twig', [
            'title' => 'Ваши транзакции',
            'items' => '10',
            'balance' => $balance,
            'topUpBalanceForm' => $topUpBalanceForm->createView()
        ]);
    }
//    #[Route('/services', name: 'services')]
//    public function new(Request $request): Response
//    {
//        // create object
//        $transaction = new Transaction();
//
//        $form = $this->createForm(SubscriptionType::class, $transaction);
//
//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
//
//            $transaction = $form->getData();
//
//            // ... perform some action, such as saving the task to the database
//
//            return $this->redirectToRoute('task_success');
//        }
//
//        return $this->renderForm('index/services.html.twig', [
//            'form' => $form,
//        ]);
//    }

}