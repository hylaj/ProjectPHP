<?php
/**
 * Rental service.
 */

namespace App\Service;

use App\Entity\Book;
use App\Entity\Rental;
use App\Entity\User;
use App\Repository\RentalRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class RentalService.
 */
class RentalService implements RentalServiceInterface
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param RentalRepository   $rentalRepository Rental repository
     * @param PaginatorInterface $paginator        Paginator service
     */
    public function __construct(private readonly RentalRepository $rentalRepository, private readonly PaginatorInterface $paginator)
    {
    }// end __construct()

    /**
     * Get paginated list by status.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByStatus(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->rentalRepository->queryByStatus(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }// end getPaginatedListByStatus()

    /**
     * Get paginated list of rentals.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->rentalRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }// end getPaginatedList()


    /**
     *  Get paginated list by user.
     *
     * @param int $page  Page number
     * @param int $owner User ID
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByOwner(int $page, int $owner): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->rentalRepository->queryByOwner($owner),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }// end getPaginatedListByOwner()


    /**
     * Get paginated list by date.
     *
     * @param int                $page Page number
     * @param \DateTimeImmutable $date Rental date
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByDate(int $page, \DateTimeImmutable $date): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->rentalRepository->queryByDate($date),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }


    /**
     * Find overdue rentals by user.
     *
     * @param User               $user User entity
     * @param \DateTimeImmutable $date Date to check overdue
     *
     * @return array|null Array of overdue rentals or null
     */
    public function findOverdueRentalsByUser(User $user, \DateTimeImmutable $date): ?array
    {
        return $this->rentalRepository->queryByDateAndUser($user, $date);
    }

    /**
     * Save entity.
     *
     * @param Rental $rental Rental entity to save
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Rental $rental): void
    {
        $this->rentalRepository->save($rental);
    }// end save()


    /**
     * Delete entity.
     * @param Rental $rental Rental entity to delete
     *
     * @return void
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Rental $rental): void
    {
        $this->rentalRepository->delete($rental);
    }// end delete()

    /**
     * Checks if Book can be rented.
     *
     * @param Book $book Book entity to check
     *
     * @return bool True if book can be rented, false otherwise
     */
    public function canBeRented(Book $book): bool
    {
        try {
            $result = $book->isAvailable();

            return false !== $result;
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }// end canBeRented()


    /**
     * Set Rental status.
     *
     * @param bool   $status Rental status to set
     * @param Rental $rental Rental entity
     *
     * @return void
     */
    public function setStatus(bool $status, Rental $rental): void
    {
        $rental->setStatus($status);
    }

    /**
     * Set Rental Details.
     *
     * @param bool   $status Status of the rental
     * @param User   $owner  User who owns the rental
     * @param Book   $book   Book rented
     * @param Rental $rental Rental entity to set details
     *
     * @return void
     */
    public function setRentalDetails(bool $status, User $owner, Book $book, Rental $rental): void
    {
        $rental->setBook($book);
        $rental->setOwner($owner);
        $rental->setStatus($status);
    }// end setRentalDetails()
}// end class
