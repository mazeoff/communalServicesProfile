<?php

namespace App\Repository;

use App\Entity\Service;
use App\Entity\Transaction;
use App\Entity\TransactionType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function save(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Transaction $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllWithFilter(?int $serviceId,?string $addition, ?\DateTime $datetime ): array
    {
        $entityManager = $this->getEntityManager();
        $queryString = 'SELECT tran FROM App\Entity\Transaction tran';
        if(isset($serviceId)){

            $queryString .=' WHERE tran.service = '.$serviceId;
            if(isset($datetime)){
                $queryString .=' AND tran.datetime LIKE \''.$datetime->format('Y-m-d').'%\'';
            }
        }else{
            if(isset($datetime)){
                $queryString .=' WHERE tran.datetime LIKE \''.$datetime->format('Y-m-d').'%\'';
            }
        }
        if(isset($addition)){
            if($addition == 'new')
                $queryString .=' order by tran.datetime desc';
            else
                $queryString .=' order by tran.datetime asc';
        }

        $query = $entityManager->createQuery($queryString);

        return $query->getResult();
    }

    public function setData(Transaction $transactionEntity, ?Service $service,TransactionType $type, float $sum, float $resultBalance,?float $quantity): void
    {
        $transactionEntity->setService($service);

        $transactionEntity->setType($type);

        $transactionEntity->setSum($sum);

        $transactionEntity->setResultBalance($resultBalance);

        $transactionEntity->setDatetime(new \DateTime());

        $transactionEntity->setQuantity($quantity);

        $this->save($transactionEntity,true);
    }

    public function findAllOrderByDESC(): array//first new
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT tran
                FROM App\Entity\Transaction tran 
                order by tran.datetime desc');
        return $query->getResult();
    }

    public function findAllOrderByASC(): array//first old
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT tran
                FROM App\Entity\Transaction tran 
                order by tran.datetime ASC');
        return $query->getResult();
    }

    public function findAllWhereServiceIdDESC($serviceId): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT tran
                FROM App\Entity\Transaction tran 
                WHERE tran.service = :id
                order by tran.datetime desc'
        )->setParameter('id', $serviceId);
        return $query->getResult();
    }

    public function findAllWhereServiceIdASC($serviceId): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT tran
                FROM App\Entity\Transaction tran 
                WHERE tran.service = :id
                order by tran.datetime asc'
        )->setParameter('id', $serviceId);
        return $query->getResult();
    }

    public function findLastTransaction(): ?Transaction
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT tran
                FROM App\Entity\Transaction tran 
                order by tran.datetime desc');
        //dd($query);
        if ($query->getResult() == null){
            return null;
        }else{
            $query->setMaxResults(1);
            return $query->getSingleResult();
        }
    }

}
