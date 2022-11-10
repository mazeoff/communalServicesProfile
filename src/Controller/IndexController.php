<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\Transaction;
use App\Form\Type\SubscriptionType;
use App\Form\Type\UnSubscriptionType;
use App\Repository\ServiceRepository;
use App\Repository\BalanceRepository;
use App\Repository\TransactionRepository;
use App\Repository\TransactionTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\Required;

class IndexController extends AbstractController
{

    private BalanceRepository $balanceRepository;
    private ServiceRepository $serviceRepository;
    private TransactionTypeRepository $transactionTypeRepository;
    private TransactionRepository $transactionRepository;

    #[Required]
    public function setBalanceRepository(BalanceRepository $balanceRepository): void
    {
        $this->balanceRepository = $balanceRepository;
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

    #[Required]
    public function setTransactionRepository(TransactionRepository $transactionRepository): void
    {
        $this->transactionRepository = $transactionRepository;
    }

    #[Route('/index', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
            'title' => 'IndexController',
        ]);
    }

    public function getTotalCostOfServices(array $items): float
    {
        $totalCostOfServices = 0;
        for ($i = 0; $i < count($items);$i++){
            if($items[$i]->isSubscription()){//если на услугу есть подписка
                $totalCostOfServices += $items[$i]->getPrice()*$items[$i]->getQuantity();//то считаем общую стоимость
            }

        }
        $totalCostOfServices *= 30;
        return $totalCostOfServices;
    }

    public function getCostOfService(array $service): float
    {
        $totalCostOfServices += $items[$i]->getPrice()*$items[$i]->getQuantity();//то считаем общую стоимость

        $totalCostOfServices *= 30;
        return $totalCostOfServices;
    }

    public function addTransaction(Integer $typeOfTransactionId, float $costOfService)
    {
        $transactionEntity = new Transaction();

        //$transactionType = new TransactionType();
        $transactionTypeEntity = $this->transactionTypeRepository->find($typeOfTransactionId);
        $balanceEntity = $this->balanceRepository->find(1);

        $transactionEntity->setType($transactionTypeEntity);//type

        $currentBalance = $balanceEntity->getValue();
        $currentBalance -= $costOfService;
        $balanceEntity->setValue($currentBalance);

        $transactionEntity->setResultingBalance($balanceEntity);//resulting balance

        $transactionEntity->setSum($costOfService);

        $this->transactionRepository->save($transactionEntity);
        $this->balanceRepository->save($balanceEntity, true);

    }

    public function subscribeToService(FormInterface $subscriptionForm )
    {
        $subscriptionDataForm = $subscriptionForm->getData();
        $service = $this->serviceRepository->find($subscriptionDataForm->getService()->getId());
        $service->setSubscription(true);
        $service->setQuantity($subscriptionForm->get('quantity')->getData());

        $this->serviceRepository->save($service,true);

        $subscriptionDataForm->getService()->get

    }

    public function unSubscribeFromService(FormInterface $unsubscriptionForm )
    {
        $serviceId = $unsubscriptionForm->get('serviceId')->getData();
        $service =  $this->serviceRepository->find($serviceId);
        $service->setSubscription(false);
        $service->setQuantity(null);

        $this->serviceRepository->save($service,true);

    }



    #[Route('/services', name: 'services')]
    public function services(Request $request): Response
    {

        $items = $this->serviceRepository->findAll();
        $balance = $this->balanceRepository->find(1)->getValue();

        //считаем общую стоимость всех услуг за месяц
        $totalCostOfServices = $this->getTotalCostOfServices($items);

        // create object
        $subscriptionTransaction = new Transaction();
        $unsubscriptionTransaction = new Transaction();


        $subscriptionForm = $this->createForm(SubscriptionType::class, $subscriptionTransaction);
        $unsubscriptionForm = $this->createForm(UnSubscriptionType::class, $unsubscriptionTransaction);

        $subscriptionForm->handleRequest($request);
        $unsubscriptionForm->handleRequest($request);



        if ($subscriptionForm->isSubmitted() && $subscriptionForm->isValid()) {
            $this->subscribeToService($subscriptionForm);
            $costOfService = $this->subscriptionForm->ge
            $this->addTransaction(2,)
            return $this->redirectToRoute('services');
        }

        if ($unsubscriptionForm->isSubmitted() && $unsubscriptionForm->isValid()) {
            $this->unSubscribeFromService($unsubscriptionForm);
            return $this->redirectToRoute('services');
        }

        return $this->render('index/services.html.twig', [
            'title' => 'Мои услуги',
            'items' => $items,
            'balance' => $balance,
            'totalCost' => $totalCostOfServices,
            'subscriptionForm' => $subscriptionForm->createView(),
            'unsubscriptionForm' => $unsubscriptionForm->createView()
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

}
