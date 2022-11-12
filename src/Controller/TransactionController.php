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
use JetBrains\PhpStorm\NoReturn;
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
    private ManagerRegistry $managerRegistry;

    private float $currentBalance;

    /**
     * @param TransactionRepository $transactionRepository
     */
    public function __construct(TransactionRepository $transactionRepository)
    {
        if($transactionRepository->findLastTransaction() == null){
            $this->currentBalance = 0;
        }else{
            $this->currentBalance = $transactionRepository->findLastTransaction()->getResultBalance();
        }
    }


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


    public function topUpBalance(float $refill, Transaction $transactionEntity )
    {
        $transactionTypeEntity = $this->transactionTypeRepository->find(1);

        $transactionEntity->setType($transactionTypeEntity);//type

        $this->currentBalance += $refill;

        $transactionEntity->setSum($refill);

        $transactionEntity->setResultBalance($this->currentBalance);//resultBalance

        $transactionEntity->setDatetime(new \DateTime());

        //dd($transactionEntity);
        $this->transactionRepository->save($transactionEntity,true);

    }


    #[Route('/transactions', name: 'transactions')]
    public function transactions(Request $request): Response
    {
        //создание формы
        $topUpBalanceTransaction = new Transaction();
        $topUpBalanceForm = $this->createForm(TopUpBalanceType::class, $topUpBalanceTransaction);

        $topUpBalanceForm->handleRequest($request);

        //обработка полченных данных от формы
        if ($topUpBalanceForm->isSubmitted() && $topUpBalanceForm->isValid()) {

            $refill = $topUpBalanceForm->get('value')->getData();
            $this->topUpBalance($refill, $topUpBalanceTransaction);

            return $this->redirectToRoute('transactions');
        }

        $transactions = $this->transactionRepository->findAll();

        //dd($transactions);

        return $this->render('index/transactions.html.twig', [
            'title' => 'Ваши транзакции',
            'transactions' => $transactions,
            'balance' => $this->currentBalance,
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