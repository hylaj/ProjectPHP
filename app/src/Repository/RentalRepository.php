<?php
/**
 * Rental repository.
 */

namespace App\Repository;

use App\Entity\Rental;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Rental Repository.
 *
 * @extends ServiceEntityRepository<Rental>
 */
class RentalRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rental::class);
    }// end __construct()

    /**
     * Save entity.
     *
     * @param Rental $rental
     *
     * @return void
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Rental $rental): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($rental);
        $this->_em->flush();
    }// end save()

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
    }// end delete()

    /**
     * Query All Rentals.
     *
     * @return QueryBuilder
     */
    public function QueryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select(
                'partial rental.{id, owner, book, status, rentalDate, comment, returnDate}',
                'partial user.{id, email}',
                'partial book.{id, title}'
            )
            ->join('rental.owner', 'user')
            ->join('rental.book', 'book');
    }

    /**
     * Query Rentals By Status.
     *
     * @return QueryBuilder
     */
    public function queryByStatus(): QueryBuilder
    {
        return $this->QueryAll()
            ->where('rental.status= :status')
            ->setParameter('status', false);
    }// end queryByStatus()

    /**
     * Query Rentals By Owner.
     *
     * @param $owner
     * @return QueryBuilder
     */
    public function queryByOwner($owner): QueryBuilder
    {
        return $this->QueryAll()
            ->where('rental.status= :status')
           /* ->setParameter('status', true) */
            ->Where('rental.owner= :owner')
            ->setParameter('owner', $owner)
            ->orderBy('rental.status', 'DESC');
    }// end queryByOwner()

    /**
     * Query Rentals By Date (overdue rentals).
     *
     * @param $date
     * @return QueryBuilder
     */
    public function queryByDate($date): QueryBuilder
    {
        return $this->QueryAll()
            ->where('rental.returnDate <= :date')
            ->setParameter('date', $date)
            ->orderBy('rental.returnDate', 'ASC');
    }

    /**
     * Query Overdue Rentals By User.
     *
     * @param $user
     * @param $date
     * @return array|null
     */
    public function queryByDateAndUser($user, $date): ?array
    {
        return $this->QueryAll()
            ->where('rental.returnDate <= :date')
            ->andWhere('rental.owner = :user')
            ->setParameter('user', $user)
            ->setParameter('date', $date)
            ->orderBy('rental.returnDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('rental');
    }// end getOrCreateQueryBuilder()


}// end class
