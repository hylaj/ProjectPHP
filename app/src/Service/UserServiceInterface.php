<?php
/**
 * User service interface.
 */

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * Interface UserServiceInterface.
 */
interface UserServiceInterface
{
    /**
     * Upgrade user's password.
     *
     * @param PasswordAuthenticatedUserInterface $user              User entity implementing PasswordAuthenticatedUserInterface
     * @param string                             $newHashedPassword New hashed password
     *
     * @return void
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void;

    /**
     * Save user entity.
     *
     * @param User $user User entity to save
     *
     * @return void
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(User $user): void;

    /**
     * Get paginated list of users.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list of users
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Check if user can be demoted.
     *
     * @param string $role Role of the user
     *
     * @return bool True if user can be demoted, false otherwise
     */
    public function canBeDemoted(string $role): bool;
}
