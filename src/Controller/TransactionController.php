<?php

namespace App\Controller;

use App\Controller\IndexController;
use App\Entity\Transaction;
use App\Form\Type\SubscriptionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
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