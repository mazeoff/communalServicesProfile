<?php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Service>
 *
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service|null findOneBy(array $criteria, array $orderBy = null)
 * @method Service[]    findAll()
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    public function save(Service $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Service $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllSubscriptions()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.subscription = true')
            ->getQuery()
            ->execute()
            ;
    }

    public function findAllUnsubscribedServices()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.subscription = false')
            ->getQuery()
            ->execute()
            ;
    }

    public function getUnitByServiceId($id)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.id = :id' )
            ->setParameter('id', $id)
            ->getQuery()
            ->execute()
            ;
    }

    public function getTotalCostOfServices(): float
    {
        $items = $this->findAllSubscriptions();
        $totalCostOfServices = 0;
        for ($i = 0; $i < count($items);$i++){
            $totalCostOfServices += $items[$i]->getPrice()*$items[$i]->getQuantity()*date('t');//то считаем общую стоимость
        }
        return $totalCostOfServices;
    }

//    /**
//     * @return Service[] Returns an array of Service objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Service
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
