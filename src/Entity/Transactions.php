<?php

namespace App\Entity;

use App\Repository\TransactionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionsRepository::class)]
class Transactions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?services $service = null;


    #[ORM\Column]
    private ?float $sum = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?balance $resultingBalance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService(): ?services
    {
        return $this->service;
    }

    public function setService(?services $service): self
    {
        $this->service = $service;

        return $this;
    }


    public function getSum(): ?float
    {
        return $this->sum;
    }

    public function setSum(float $sum): self
    {
        $this->sum = $sum;

        return $this;
    }

    public function getResultingBalance(): ?balance
    {
        return $this->resultingBalance;
    }

    public function setResultingBalance(?balance $resultingBalance): self
    {
        $this->resultingBalance = $resultingBalance;

        return $this;
    }
}
