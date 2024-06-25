<?php
/**
 * User service interface.
 */

namespace App\Service;

use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * Interface UserServiceInterface.
 */
interface UserServiceInterface
{

    /**
     * Upgrade password.
     *
     * @param PasswordAuthenticatedUserInterface $user
     * @param string $newHashedPassword
     * @return void
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void;

    /**
     * Save entity.
     *
     * @param User $user
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(User $user): void;

    /**
     * Get paginated list.
     *
     * @param int $page
     * @return PaginationInterface
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Can user be demoted?
     *
     * @param string $role
     * @return bool
     */
    public function canBeDemoted(string $role): bool;
}
