<?php

namespace App\Form\Type;

use App\Entity\Service;
use App\Entity\Transaction;
use App\Repository\ServiceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class SubscriptionType extends AbstractType
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
                    return sprintf('%s (%s)', $service->getName(), $service->getUnit().': '.$service->getPrice()."₽ / день");
                },
                'choices' => $this->serviceRepository->findAllUnsubscribedServices(),
                'label'   => 'Услуга'
            ])
//            ->add('unit', TextType::class,[
//                'data'=>function(Service $service) {
//                   // return sprintf('%s (%s)', $service->getName(), $service->getUnit());
//                    return $service->getUnit();
//                },
//                'disabled' => true,
//                'label'   => 'Цена ',
//                'mapped' => false
//            ])
            ->add('quantity', TextType::class,[
                'label' => 'Количество',
                'mapped' => false

            ])
            ->add('submit', SubmitType::class,[
            'label'   => 'Подписаться'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}