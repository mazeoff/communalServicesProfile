<?php

namespace App\Controller;

use App\Controller\IndexController;
use App\Entity\Balance;
use App\Entity\Service;
use App\Entity\Transaction;
use App\Entity\TransactionType;
use App\Form\Type\SubscriptionType;
use App\Form\Type\TopUpBalanceType;
use App\Form\Type\TransactionFilterByServicesType;
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
    private ServiceRepository $serviceRepository;
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
    public function setServiceRepository(ServiceRepository $serviceRepository): void
    {
        $this->serviceRepository = $serviceRepository;
    }

    #[Required]
    public function setTransactionTypeRepository(TransactionTypeRepository $transactionTypeRepository): void
    {
        $this->transactionTypeRepository = $transactionTypeRepository;
    }


    public function topUpBalance(float $refill, Transaction $transactionEntity )
    {
        $transactionTypeEntity = $this->transactionTypeRepository->find(1);

        $this->currentBalance += $refill;

        $this->transactionRepository->setData($transactionEntity, null,$transactionTypeEntity, $refill,$this->currentBalance, null);

    }


    #[Route('/transactions', name: 'transactions')]
    public function transactions(Request $request): Response
    {
        if (isset($_POST['settlementDay']))
        {
            $totalCostOfServices = $this->serviceRepository->getTotalCostOfServices();
            if ($totalCostOfServices > $this->currentBalance){
                $response = new Response('Error');
                return $response;
            }else{
                $transactionTypeEntity = $this->transactionTypeRepository->find(2);

                $services = $this->serviceRepository->findAllSubscriptions();

                foreach ($services as $service){
                    $quantity = $service->getQuantity();
                    $costOfService = $service->getPrice()*$quantity*date('t');
                    $this->currentBalance -= $costOfService;
                    $this->transactionRepository->setData(new Transaction(),
                                                            $service,
                                                            $transactionTypeEntity,
                                                            $costOfService,
                                                            $this->currentBalance,
                                                            $quantity);
                }
            }

        }

        $transactions = $this->transactionRepository->findAllOrderByDESC();


        //создание формы Фильтра
        $transactionFilterByServicesForm = $this->createForm(TransactionFilterByServicesType::class);

        $transactionFilterByServicesForm->handleRequest($request);

        if ($transactionFilterByServicesForm->isSubmitted())
        {
            $transactionFilterByServicesDataForm = $transactionFilterByServicesForm->getData();
            $serviceId = $transactionFilterByServicesDataForm->getService()->getId();
            if($transactionFilterByServicesForm->get('addition')->getData() == 'new')
                $transactions = $this->transactionRepository->findAllWhereServiceIdDESC($serviceId);
            if($transactionFilterByServicesForm->get('addition')->getData() == 'old')
                $transactions = $this->transactionRepository->findAllWhereServiceIdASC($serviceId);

        }


        //создание формы Пополнения баланса
        $topUpBalanceTransaction = new Transaction();
        $topUpBalanceForm = $this->createForm(TopUpBalanceType::class, $topUpBalanceTransaction);

        $topUpBalanceForm->handleRequest($request);

        //обработка полченных данных от формы
        if ($topUpBalanceForm->isSubmitted() && $topUpBalanceForm->isValid()) {

            $refill = $topUpBalanceForm->get('value')->getData();
            $this->topUpBalance($refill, $topUpBalanceTransaction);

            return $this->redirectToRoute('transactions');
        }


        //dd($transactions);

        return $this->render('index/transactions.html.twig', [
            'title' => 'Ваши транзакции',
            'transactions' => $transactions,
            'balance' => $this->currentBalance,
            'topUpBalanceForm' => $topUpBalanceForm->createView(),
            'transactionFilterByServicesForm' =>$transactionFilterByServicesForm->createView(),
        ]);
    }

}