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
    private const PAGINATOR_ITEMS_PER_PAGE = 10;


    /**
     * Constructor.
     *
     * @param UserRepository $userRepository User repository
     */
    public function __construct(private readonly UserRepository $userRepository, private readonly PaginatorInterface $paginator)
    {

    }//end __construct()


    /**
     * Update password.
     *
     * @param PasswordAuthenticatedUserInterface $user
     * @param string                             $newHashedPassword
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

    }//end upgradePassword()


    /**
     * Save entity.
     *
     * @param User $user User entity
     */
    public function save(User $user): void
    {
        /*
            if(null === $category->getId()){
            $category->setCreatedAt(new \DateTimeImmutable());
            }
            $category->setCreatedAt(new \DateTimeImmutable());
        */

        $this->userRepository->save($user);

    }//end save()


    public function getPaginatedList(int $page): PaginationInterface {
        return $this->paginator->paginate(
            $this->userRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }
}//end class
