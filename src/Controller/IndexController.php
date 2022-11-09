<?php

namespace App\Controller;

use App\Entity\Service;
use App\Entity\Transaction;
use App\Form\Type\SubscriptionType;
use App\Form\Type\UnSubscriptionType;
use App\Repository\ServiceRepository;
use App\Repository\BalanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function services(Request           $request,
                             ServiceRepository $servicesRepository,
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
        $subscriptionTransaction = new Transaction();
        $unsubscriptionTransaction = new Transaction();


        $subscriptionForm = $this->createForm(SubscriptionType::class, $subscriptionTransaction);
        $unsubscriptionForm = $this->createForm(UnSubscriptionType::class, $unsubscriptionTransaction);

        $subscriptionForm->handleRequest($request);
        $unsubscriptionForm->handleRequest($request);



        if ($subscriptionForm->isSubmitted() && $subscriptionForm->isValid()) {
            $subscriptionDataForm = $subscriptionForm->getData();
            $service = $servicesRepository->find($subscriptionDataForm->getService()->getId());
            $service->setSubscription(true);
            $service->setQuantity($subscriptionForm->get('quantity')->getData());

            $servicesRepository->save($service,true);

            return $this->redirectToRoute('services');
        }

        if ($unsubscriptionForm->isSubmitted() && $unsubscriptionForm->isValid()) {
            $serviceId = $unsubscriptionForm->get('serviceId')->getData();
            $service =  $servicesRepository->find($serviceId);
            $service->setSubscription(false);
            $service->setQuantity(null);

            $servicesRepository->save($service,true);

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
