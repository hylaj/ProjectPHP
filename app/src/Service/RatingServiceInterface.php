<?php
/**
 * Rating service interface.
 */

namespace App\Service;

use App\Entity\Book;
use App\Entity\Rating;
use App\Entity\User;

/**
 * Interface BookServiceInterface.
 */
interface RatingServiceInterface
{
    public function save(Rating $rating): void;

    public function delete(Rating $rating): void;

    public function canBeRated(Book $book, User $user): bool;

    public function findAverageRatingAndCountByBook(int $bookId): ?array;

    public function getRatingByUserAndBook(User $user, Book $book): ?Rating;
}
