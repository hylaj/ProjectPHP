<?php
/**
 * Rental service interface.
 */

namespace App\Service;

use App\Entity\Book;
use App\Entity\Rental;
use App\Entity\User;
use DateTimeImmutable;
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
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
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
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Rental $rental): void;

    public function setRentalDetails(bool $status, User $owner, Book $book, Rental $rental): void;

    public function canBeRented(Book $book): bool;

    public function setStatus(bool $status, Rental $rental): void;

    public function getPaginatedListByDate(int $page, $date): PaginationInterface;
    public function findOverdueRentalsByUser(User $user, DateTimeImmutable $date): ?array;
}
