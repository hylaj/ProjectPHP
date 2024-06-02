<?php
/**
 * Rental service interface.
 */

namespace App\Service;
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
     * @param int  $page   Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByStatus(int $page): PaginationInterface;


    /**
     * Get paginated list by user.
     *
     * @param int  $page   Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedListByOwner(int $page, int $owner): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Rental $rental Rental entity
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Rental $rental): void;

    /**
     * Delete entity.
     *
     * @param Rental $rental
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Rental $rental): void;


}