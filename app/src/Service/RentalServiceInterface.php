<?php
/**
 * Rental service interface.
 */

namespace App\Service;

use App\Entity\Book;
use App\Entity\Rental;
use App\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface RentalServiceInterface.
 */
interface RentalServiceInterface
{
    /**
     * Get paginated list by status.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByStatus(int $page): PaginationInterface;


    /**
     * Get paginated list by user.
     *
     * @param int $page
     * @param int $owner
     *
     * @return PaginationInterface
     */
    public function getPaginatedListByOwner(int $page, int $owner): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Rental $rental Rental entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Rental $rental): void;


    /**
     * Delete entity.
     *
     * @param Rental $rental
     *
     * @return void
     */
    public function delete(Rental $rental): void;

    /**
     * Set Rental Details.
     *
     * @param bool   $status
     * @param User   $owner
     * @param Book   $book
     * @param Rental $rental
     *
     * @return void
     */
    public function setRentalDetails(bool $status, User $owner, Book $book, Rental $rental): void;

    /**
     * Checks if Book can be rented.
     *
     * @param Book $book
     *
     * @return bool
     */
    public function canBeRented(Book $book): bool;

    /**
     * Sets the rental status.
     *
     * @param bool   $status
     * @param Rental $rental
     *
     * @return void
     */
    public function setStatus(bool $status, Rental $rental): void;

    /**
     * Get paginated list of rentals by date.
     *
     * @param int $page
     * @param $date
     *
     * @return PaginationInterface
     */
    public function getPaginatedListByDate(int $page, $date): PaginationInterface;

    /**
     * Find overdue rentals bu user.
     *
     * @param User               $user
     * @param \DateTimeImmutable $date
     *
     * @return array|null
     */
    public function findOverdueRentalsByUser(User $user, \DateTimeImmutable $date): ?array;
}
