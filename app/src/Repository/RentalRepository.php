<?php

namespace App\Repository;

use App\Entity\Rental;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rental>
 */
class RentalRepository extends ServiceEntityRepository
{


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rental::class);

    }//end __construct()


    /**
     * @param Rental $rental
     *
     * @return void
     */
    public function save(Rental $rental): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($rental);
        $this->_em->flush();

    }//end save()


    /**
     * Delete entity.
     *
     * @param Rental $rental Rental entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Rental $rental): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($rental);
        $this->_em->flush();

    }//end delete()


    public function QueryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select(
            'partial rental.{id, owner, book, status, rentalDate}',
            'partial user.{id, email}',
            'partial book.{id, title}'
        )
            ->join('rental.owner', 'user')
            ->join('rental.book', 'book');
    }


    /**
     * @return QueryBuilder
     */
    public function queryByStatus(): QueryBuilder
    {
        return $this->QueryAll()
            ->where('rental.status= :status')
            ->setParameter('status', false);

    }//end queryByStatus()


    public function queryByOwner($owner): QueryBuilder
    {
        return $this->QueryAll()
            ->where('rental.status= :status')
            ->setParameter('status', true)
            ->andWhere('rental.owner= :owner')
            ->setParameter('owner', $owner);

    }//end queryByOwner()


    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $queryBuilder=null): QueryBuilder
    {
        return ($queryBuilder ?? $this->createQueryBuilder('rental'));

    }//end getOrCreateQueryBuilder()


    // **
    // * @return Rental[] Returns an array of Rental objects
    // */
    // public function findByExampleField($value): array
    // {
    // return $this->createQueryBuilder('r')
    // ->andWhere('r.exampleField = :val')
    // ->setParameter('val', $value)
    // ->orderBy('r.id', 'ASC')
    // ->setMaxResults(10)
    // ->getQuery()
    // ->getResult()
    // ;
    // }
    // public function findOneBySomeField($value): ?Rental
    // {
    // return $this->createQueryBuilder('r')
    // ->andWhere('r.exampleField = :val')
    // ->setParameter('val', $value)
    // ->getQuery()
    // ->getOneOrNullResult()
    // ;
    // }
}//end class
