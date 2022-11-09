<?php

namespace App\Controller;

use App\Controller\IndexController;
use App\Entity\Balance;
use App\Entity\Service;
use App\Entity\Transaction;
use App\Entity\TransactionType;
use App\Form\Type\SubscriptionType;
use App\Form\Type\TopUpBalanceType;
use App\Repository\BalanceRepository;
use App\Repository\ServiceRepository;
use App\Repository\TransactionRepository;
use App\Repository\TransactionTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\Required;

class TransactionController extends AbstractController
{

//    public function show(ManagerRegistry $doctrine, int $id): Response
//    {
//        $transactionEntity = $doctrine->getRepository(Service::class)->find($id);
//
//    }

    private TransactionRepository $transactionRepository;
    private TransactionTypeRepository $transactionTypeRepository;
    private BalanceRepository $balanceRepository;
    private ManagerRegistry $managerRegistry;


    #[Required]
    public function setManagerRegistry(ManagerRegistry $managerRegistry): void
    {
        $this->managerRegistry = $managerRegistry;
    }

    #[Required]
    public function setTransactionRepository(TransactionRepository $transactionRepository): void
    {
        $this->transactionRepository = $transactionRepository;
    }

    #[Required]
    public function setTransactionTypeRepository(TransactionTypeRepository $transactionTypeRepository): void
    {
        $this->transactionTypeRepository = $transactionTypeRepository;
    }

    #[Required]
    public function setBalanceRepository(BalanceRepository $balanceRepository): void
    {
        $this->balanceRepository = $balanceRepository;
    }

    public function topUpBalance(float $refill )
    {
        $transactionEntity = new Transaction();

        //$transactionType = new TransactionType();
        $transactionTypeEntity = $this->transactionTypeRepository->find(1);
        $balanceEntity = $this->balanceRepository->find(1);

        $transactionEntity->setType($transactionTypeEntity);//type

        $currentBalance = $balanceEntity->getValue();
        $currentBalance += $refill;
        $balanceEntity->setValue($currentBalance);

        $transactionEntity->setResultingBalance($balanceEntity);//resulting balance

        $transactionEntity->setSum($refill);

        $this->transactionRepository->save($transactionEntity);
        $this->balanceRepository->save($balanceEntity, true);

    }


    #[Route('/transactions', name: 'transactions')]
    public function transactions(Request $request): Response
    {
        $currentBalance = $this->balanceRepository->find(1)->getValue();
        //создание формы
        $topUpBalanceTransaction = new Transaction();
        $topUpBalanceForm = $this->createForm(TopUpBalanceType::class, $topUpBalanceTransaction);

        $topUpBalanceForm->handleRequest($request);

        //обработка полченных данных от формы
        if ($topUpBalanceForm->isSubmitted() && $topUpBalanceForm->isValid()) {

            $refill = $topUpBalanceForm->get('value')->getData();
            $this->topUpBalance($refill);

            return $this->redirectToRoute('transactions');
        }


        $transactions = $this->transactionRepository->findAll();
        dd($transactions);

        return $this->render('index/transactions.html.twig', [
            'title' => 'Ваши транзакции',
            'transactions' => $transactions,
            'balance' => $currentBalance,
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