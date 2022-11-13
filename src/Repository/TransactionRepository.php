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

    public function findOneByIdJoinedToType(int $typeId): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT type, tran
                FROM App\Entity\Transaction tran 
                INNER JOIN App\Entity\TransactionType type
                WHERE type.id = :id'
        )->setParameter('id', $typeId);

        //$query->setFetchMode(Transaction::class, 'transaction', ClassMetadataInfo::FETCH_EAGER);
        dd($query->getResult());

        return $query->getResult();
    }





//    /**
//     * @return Transaction[] Returns an array of Transaction objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Transaction
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
