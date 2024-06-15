<?php
/**
 * Rating service.
 */

namespace App\Service;

use App\Dto\BookListFiltersDto;
use App\Dto\BookListInputFiltersDto;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Rating;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\RatingRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class RatingService.
 */
class RatingService implements RatingServiceInterface
{
    /**
     * Constructor.
     *
     * @param RatingRepository     $ratingRepository Rating repository
     * @param PaginatorInterface $paginator      Paginator
     */
    public function __construct(
        private readonly RatingRepository $ratingRepository
    ) {
    }// end __construct()

    /**
     * Save entity.
     *
     * @param Rating $rating Rating entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Rating $rating): void
    {
        $this->ratingRepository->save($rating);
    }// end save()

    /**
     * Delete entity.
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Rating $rating): void
    {
        $this->ratingRepository->delete($rating);
    }

    public function canBeRated(Book $book, User $user): bool
    {
        try {
            $result = $this->ratingRepository->findOneBy(['book' => $book, 'user' => $user]);

            return $result === null;
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }// end canBeRented()

    public function findAverageRatingAndCountByBook(int $bookId): ?array
    {
        return $this->ratingRepository->findAverageRatingAndCountByBook($bookId);
    }
public function getRatingByUserAndBook(User $user, Book $book): ?Rating
{
    return $this->ratingRepository->findOneBy(['book' => $book, 'user' => $user]);
}

}