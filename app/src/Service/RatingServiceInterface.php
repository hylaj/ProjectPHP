<?php
/**
 * Rating service interface.
 */

namespace App\Service;

use App\Entity\Book;
use App\Entity\Rating;
use App\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

/**
 * Interface BookServiceInterface.
 */
interface RatingServiceInterface
{
    /**
     * Save entity.
     *
     * @param Rating $rating Rating entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Rating $rating): void;

    /**
     * Delete entity.
     *
     * @param Rating $rating Rating
     *
     * @return void
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Rating $rating): void;

    /**
     * Find Average Rating and count ratings by book.
     *
     * @param int $bookId Book ID
     *
     * @return array|null array
     */
    public function findAverageRatingAndCountByBook(int $bookId): ?array;

    /**
     * Get rating by user and book.
     *
     * @param User $user User entity
     * @param Book $book Book entity
     *
     * @return Rating|null Rating entity
     */
    public function getRatingByUserAndBook(User $user, Book $book): ?Rating;
}
