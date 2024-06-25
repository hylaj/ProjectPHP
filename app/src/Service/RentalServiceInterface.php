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
     *  Get paginated list by user.
     *
     * @param int $page  Page number
     * @param int $owner User ID
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByOwner(int $page, int $owner): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Rental $rental Rental entity to save
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Rental $rental): void;

    /**
     * Delete entity.
     *
     * @param Rental $rental Rental entity to delete
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Rental $rental): void;

    /**
     * Set Rental Details.
     *
     * @param bool   $status Status of the rental
     * @param User   $owner  User who owns the rental
     * @param Book   $book   Book rented
     * @param Rental $rental Rental entity to set details
     */
    public function setRentalDetails(bool $status, User $owner, Book $book, Rental $rental): void;

    /**
     * Checks if Book can be rented.
     *
     * @param Book $book Book entity to check
     *
     * @return bool True if book can be rented, false otherwise
     */
    public function canBeRented(Book $book): bool;

    /**
     * Set Rental status.
     *
     * @param bool   $status Rental status to set
     * @param Rental $rental Rental entity
     */
    public function setStatus(bool $status, Rental $rental): void;

    /**
     * Get paginated list by date.
     *
     * @param int                $page Page number
     * @param \DateTimeImmutable $date Rental date
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByDate(int $page, \DateTimeImmutable $date): PaginationInterface;

    /**
     * Find overdue rentals by user.
     *
     * @param User               $user User entity
     * @param \DateTimeImmutable $date Date to check overdue
     *
     * @return array|null Array of overdue rentals or null
     */
    public function findOverdueRentalsByUser(User $user, \DateTimeImmutable $date): ?array;
}
