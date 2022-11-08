<?php

namespace App\Form\Type;

use App\Entity\Service;
use App\Entity\Transaction;
use App\Repository\ServiceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class TransactionType extends AbstractType
{
    private ServiceRepository $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('service', EntityType::class,[
                'class' => Service::class,
                'choice_label' => function(Service $service) {
//                    return sprintf('(%d) %s', $service-> getName(), $service->getQuantity());
                    return $service-> getName();
                },
                'choices' => $this->serviceRepository->findAllUnsubscribedServices(),
                'label'   => 'Услуга'
            ])
            ->add('unit', EntityType::class,[
                'class' => Service::class,
                'choice_label' => function(Service $service) {
                    return $service-> getUnit();
                },
                'choices' => $this->serviceRepository->findAllUnsubscribedServices(),
                'label'   => 'Услуга'
            ])
            ->add('quantity', TextType::class,[
                'label' => 'Количество',
                'mapped' => false

            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}