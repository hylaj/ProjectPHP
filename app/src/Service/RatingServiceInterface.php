<?php
/**
 * Rating service interface.
 */

namespace App\Service;

use App\Dto\BookListInputFiltersDto;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Rating;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface BookServiceInterface.
 */
interface RatingServiceInterface
{
    public function save(Rating $rating): void;
    public function delete(Rating $rating): void;
    public function canBeRated(Book $book, User $user): bool;
    public function getAverageRatingByBook(int $bookId): ?float;

    public function getRatingByBook(int $bookId);
}