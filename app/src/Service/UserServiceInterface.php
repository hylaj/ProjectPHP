<?php
/**
 * User service interface.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * Interface CategoryServiceInterface.
 */
interface UserServiceInterface
{

    /**
     * @param User $user
     * @param string $newHashedPassword
     * @return void
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void;
}