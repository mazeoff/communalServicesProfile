<?php

namespace App\Form\Type;

use App\Entity\Service;
use App\Entity\Transaction;
use App\Repository\ServiceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionFilterByServicesType extends AbstractType
{
    private ServiceRepository $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('addition', ChoiceType::class, [
                'choices'  => [
                    'Новые' => 'new',
                    'Старые' => 'old'
                ],
                'mapped' => false,
                'label'   => false
            ])
            ->add('service', EntityType::class,[
                'class' => Service::class,
                'choice_label' => function(Service $service) {
                    return $service->getName();
                },
                'choices' => $this->serviceRepository->findAllSubscriptions(),
//                'placeholder' => 'По услуге',
                'required'   => false,
                'empty_data'=>'По услуге',
                'label'   => false,
            ])
            ->add('publishedAt', DateType::class, [
                // renders it as a single text box
//                'widget' => 'single_text',
                'label'   => false,
                'mapped' => false,
                ])

            ->add('submit', SubmitType::class,[
                'label'   => 'Обновить'
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