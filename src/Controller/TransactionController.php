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
use App\Repository\ServiceRepository;
use App\Repository\TransactionRepository;
use App\Repository\TransactionTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\Required;

class TransactionController extends AbstractController
{



    private TransactionRepository $transactionRepository;
    private ServiceRepository $serviceRepository;
    private TransactionTypeRepository $transactionTypeRepository;

    private float $currentBalance;

    /**
     * @param TransactionRepository $transactionRepository
     */
    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->currentBalance = $transactionRepository->getBalance();
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
        if ($request->request->get('settlementDay') == '1')
        {
            $totalCostOfServices = $this->serviceRepository->getTotalCostOfServices();
            if ($totalCostOfServices > $this->currentBalance){
                $response = new Response('Error');
                $response->headers->set('Content-Type', 'text/plain');
                $response->send();
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

            if($transactionFilterByServicesDataForm->getService() != null)
               $serviceId = $transactionFilterByServicesDataForm->getService()->getId();
            else
                $serviceId = null;

            $transactions = $this->transactionRepository->findAllWithFilter(    $serviceId,
                                                                                $transactionFilterByServicesForm->get('addition')->getData(),
                                                                                $transactionFilterByServicesForm->get('publishedAt')->getData());


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