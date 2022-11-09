<?php

namespace App\Form\Type;

use App\Entity\Balance;
use App\Entity\Transaction;
use App\Repository\BalanceRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TopUpBalanceType extends AbstractType
{
    private BalanceRepository $balanceRepository;

    public function __construct(BalanceRepository $balanceRepository)
    {
        $this->balanceRepository = $balanceRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value', MoneyType::class,[
                'mapped'=> false,
                'currency' => 'RUB',
                'label' => 'Сумма'
            ])
            ->add('submit', SubmitType::class,[
                'label'   => 'Пополнить'
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