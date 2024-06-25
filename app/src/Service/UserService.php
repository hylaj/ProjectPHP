<?php
/**
 * User service.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * Class UserService.
 */
class UserService implements UserServiceInterface
{
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param UserRepository     $userRepository User repository
     * @param PaginatorInterface $paginator      Paginator service
     */
    public function __construct(private readonly UserRepository $userRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Upgrade user's password.
     *
     * @param PasswordAuthenticatedUserInterface $user              User entity implementing PasswordAuthenticatedUserInterface
     * @param string                             $newHashedPassword New hashed password
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        $this->userRepository->upgradePassword($user, $newHashedPassword);
    }

    /**
     * Save user entity.
     *
     * @param User $user User entity to save
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * Get paginated list of users.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list of users
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->userRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Check if user can be demoted.
     *
     * @param string $role Role of the user
     *
     * @return bool True if user can be demoted, false otherwise
     */
    public function canBeDemoted(string $role): bool
    {
        try {
            $result = $this->userRepository->countByRole($role);

            return $result > 2;
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }
}
