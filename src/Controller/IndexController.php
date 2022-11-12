<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\Transaction;
use App\Form\Type\SubscriptionType;
use App\Form\Type\UnSubscriptionType;
use App\Repository\ServiceRepository;
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

    private ServiceRepository $serviceRepository;
    private TransactionTypeRepository $transactionTypeRepository;
    private TransactionRepository $transactionRepository;
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

    public function getNumbersOfDaysBeforeSettlementDate(): int
    {
        return (int)date('t')-(int)date('j')+1;
    }

    public function getCostOfService(Service $service, int $quantity): float
    {
        return $service->getPrice()*$quantity*$this->getNumbersOfDaysBeforeSettlementDate();
    }

    public function addTransaction(int $serviceId, int $typeOfTransactionId, float $costOfService)
    {
        $transactionEntity = new Transaction();

        $transactionTypeEntity = $this->transactionTypeRepository->find($typeOfTransactionId);

        $serviceEntity = $this->serviceRepository->find($serviceId);

        $transactionEntity->setService($serviceEntity);//serviceId

        $transactionEntity->setType($transactionTypeEntity);//type

        if ($typeOfTransactionId == 2)
            $this->currentBalance -= $costOfService;//subscribeToService
                else
                    $this->currentBalance += $costOfService;//unSubscribeFromService

        $transactionEntity->setResultBalance($this->currentBalance);

        $transactionEntity->setSum($costOfService); //sum

        $transactionEntity->setDatetime(new \DateTime());

        $transactionEntity->setQuantity($serviceEntity->getQuantity());

        $this->transactionRepository->save($transactionEntity, true);

    }

    public function subscribeToService(FormInterface $subscriptionForm ): bool
    {
        $subscriptionDataForm = $subscriptionForm->getData();
        $serviceId = $subscriptionDataForm->getService()->getId();
        $service = $this->serviceRepository->find($serviceId);

        $quantity = $subscriptionForm->get('quantity')->getData();
        $costOfService = $this->getCostOfService($service, $quantity);

        if ($costOfService > $this->currentBalance){
            return false;
        }else{
            $service->setSubscription(true);
            $service->setQuantity($quantity);

            $this->addTransaction($serviceId,2,$costOfService);

            $this->serviceRepository->save($service,true);
            return true;
        }
    }

    public function unSubscribeFromService(FormInterface $unsubscriptionForm )
    {
        $unSubscriptionDataForm = $unsubscriptionForm->getData();
        $serviceId = $unsubscriptionForm->get('serviceId')->getData();
        $service = $this->serviceRepository->find($serviceId);

        $quantity = $service->getQuantity();
        $costOfService = $this->getCostOfService($service, $quantity);

        $this->addTransaction($serviceId,3,$costOfService);

        $service->setSubscription(false);
        $service->setQuantity(null);

        $this->serviceRepository->save($service,true);

    }



    #[Route('/services', name: 'services')]
    public function services(Request $request): Response
    {
        $items = $this->serviceRepository->findAll();

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
            if ($this->subscribeToService($subscriptionForm)){//success
                return $this->redirectToRoute('services');
            }else{
                $response = new Response('Error');
                return $response;
            }
        }

        if ($unsubscriptionForm->isSubmitted() && $unsubscriptionForm->isValid()) {
            $this->unSubscribeFromService($unsubscriptionForm);
            return $this->redirectToRoute('services');
        }

        return $this->render('index/services.html.twig', [
            'title' => 'Мои услуги',
            'items' => $items,
            'balance' => $this->currentBalance,
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
