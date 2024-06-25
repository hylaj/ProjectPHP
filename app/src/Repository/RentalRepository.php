<?php
/**
 * Rental repository.
 */

namespace App\Repository;

use App\Entity\Rental;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;

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
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rental::class);
    }// end __construct()

    /**
     * Save entity.
     *
     * @param Rental $rental Rental entity
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
     * Query all rentals.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select(
                'partial rental.{id, owner, book, status, rentalDate, comment, returnDate}',
                'partial user.{id, email}',
                'partial book.{id, title}'
            )
            ->join('rental.owner', 'user')
            ->join('rental.book', 'book');
    }// end queryAll()

    /**
     * Query rentals by status.
     *
     * @return QueryBuilder Query builder
     */
    public function queryByStatus(): QueryBuilder
    {
        return $this->queryAll()
            ->where('rental.status= :status')
            ->setParameter('status', false);
    }// end queryByStatus()

    /**
     * Query rentals by owner.
     *
     * @param int $owner Owner entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByOwner(int $owner): QueryBuilder
    {
        return $this->queryAll()
            ->where('rental.status= :status')
            ->where('rental.owner= :owner')
            ->setParameter('owner', $owner)
            ->orderBy('rental.status', 'DESC');
    }// end queryByOwner()

    /**
     * Query rentals by date (overdue rentals).
     *
     * @param \DateTimeImmutable $date Date to check against
     *
     * @return QueryBuilder Query builder
     */
    public function queryByDate(\DateTimeImmutable $date): QueryBuilder
    {
        return $this->queryAll()
            ->where('rental.returnDate <= :date')
            ->setParameter('date', $date)
            ->orderBy('rental.returnDate', 'ASC');
    }// end queryByDate()

    /**
     * Query overdue rentals by user.
     *
     * @param User               $user User entity
     * @param \DateTimeImmutable $date Date to check against
     *
     * @return array|null List of rentals or null
     */
    public function queryByDateAndUser(User $user, \DateTimeImmutable $date): ?array
    {
        return $this->queryAll()
            ->where('rental.returnDate <= :date')
            ->andWhere('rental.owner = :user')
            ->setParameter('user', $user)
            ->setParameter('date', $date)
            ->orderBy('rental.returnDate', 'ASC')
            ->getQuery()
            ->getResult();
    }// end queryByDateAndUser()

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
