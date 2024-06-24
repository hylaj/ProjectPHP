<?php
/**
 * Book service interface.
 */

namespace App\Service;

use App\Dto\BookListInputFiltersDto;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface BookServiceInterface.
 */
interface BookServiceInterface
{
    public function getBooksByCategory(Category $category): array;

    /**
     * Save entity.
     */
    public function save(Book $book): void;

    /**
     * Delete entity.
     */
    public function delete(Book $book): void;

    /**
     * Get paginated list.
     *
     * @param int  $page   Page number
     * @param User $author Author
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page/* , User $author */, BookListInputFiltersDto $filters): PaginationInterface;

    public function setAvailable(Book $book, bool $status): void;

    /**
     * Create avatar.
     */
    public function createCover(UploadedFile $uploadedFile, Book $book): void;

    public function updateCover(UploadedFile $uploadedFile, Book $book): void;

    public function canBeDeleted(Book $book): bool;
}
