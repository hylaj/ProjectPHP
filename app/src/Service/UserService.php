<?php
/**
 * User service.
 */

namespace App\Service;

use App\Entity\Category;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * Class CategoryService.
 */
class UserService implements UserServiceInterface
{
    /**
     * Constructor.
     *
     * @param UserRepository $userRepository User repository
     */
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * Update password.
     *
     * @param PasswordAuthenticatedUserInterface $user
     * @param string $newHashedPassword
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        /*
        if(null === $category->getId()){
            $category->setCreatedAt(new \DateTimeImmutable());
        }
        $category->setCreatedAt(new \DateTimeImmutable());
        */

        $this->userRepository->upgradePassword($user, $newHashedPassword);

    }
}