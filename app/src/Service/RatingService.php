<?php
/**
 * Rating service.
 */

namespace App\Service;

use App\Entity\Book;
use App\Entity\Rating;
use App\Entity\User;
use App\Repository\RatingRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;

/**
 * Class RatingService.
 */
class RatingService implements RatingServiceInterface
{
    /**
     * Constructor.
     *
     * @param RatingRepository $ratingRepository Rating repository
     */
    public function __construct(private readonly RatingRepository $ratingRepository) {
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
     * @param Rating $rating
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Rating $rating): void
    {
        $this->ratingRepository->delete($rating);
    }

    /**
     * Find Average Rating and count ratings by book.
     *
     * @param int $bookId
     * @return array|null
     */
    public function findAverageRatingAndCountByBook(int $bookId): ?array
    {
        return $this->ratingRepository->findAverageRatingAndCountByBook($bookId);
    }

    /**
     * Get rating by user and book.
     *
     * @param User $user
     * @param Book $book
     * @return Rating|null
     */
    public function getRatingByUserAndBook(User $user, Book $book): ?Rating
    {
        return $this->ratingRepository->findOneBy(['book' => $book, 'user' => $user]);
    }
}
