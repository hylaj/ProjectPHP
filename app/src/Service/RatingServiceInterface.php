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
     * @param Rating $rating
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Rating $rating): void;

    /**
     * Find Average Rating and count ratings by book.
     *
     * @param int $bookId
     * @return array|null
     */
    public function findAverageRatingAndCountByBook(int $bookId): ?array;

    /**
     * Get rating by user and book.
     *
     * @param User $user
     * @param Book $book
     * @return Rating|null
     */
    public function getRatingByUserAndBook(User $user, Book $book): ?Rating;
}
