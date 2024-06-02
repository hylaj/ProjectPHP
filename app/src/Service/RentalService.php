<?php
/**
 * Rental service.
 */

namespace App\Service;

use App\Entity\Rental;
use App\Entity\User;
use App\Repository\RentalRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
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
     * @param RentalRepository     $rentalRepository Rental repository
     * @param PaginatorInterface $paginator      Paginator
     */
    public function __construct(private readonly RentalRepository $rentalRepository, private readonly PaginatorInterface $paginator)
    {    }

    /**
     * Get paginated list by status.
     *
     * @param int  $page   Page number
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
    }

    /**
     * Get paginated list by user.
     *
     * @param int  $page   Page number
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
    }

    /**
     * Save entity.
     *
     * @param Rental $rental Rental entity
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Rental $rental): void
    {
        $this->rentalRepository->save($rental);

    }//end save()

    /**
     * Delete entity.
     *
     * @param Rental $rental
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Rental $rental): void
    {
        $this->rentalRepository->delete($rental);
    }

}//end class
