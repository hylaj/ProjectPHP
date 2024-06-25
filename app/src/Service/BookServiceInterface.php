<?php
/**
 * Book service interface.
 */

namespace App\Service;

use App\Dto\BookListInputFiltersDto;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface BookServiceInterface.
 */
interface BookServiceInterface
{
    /**
     * Get Books By Category.
     *
     * @param Category $category Category entity
     *
     * @return array Array of books
     */
    public function getBooksByCategory(Category $category): array;


    /**
     * Save Book entity.
     *
     * @param Book $book Book entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Book $book): void;

    /**
     * Delete Book entity.
     *
     * @param Book $book Book entity
     *
     * @return void
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Book $book): void;

    /**
     * Get Paginated List.
     *
     * @param int                     $page
     * @param BookListInputFiltersDto $filters
     *
     * @return PaginationInterface
     */
    public function getPaginatedList(int $page/* , User $author */, BookListInputFiltersDto $filters): PaginationInterface;

    /**
     * Sets availability of book.
     *
     * @param Book $book   Book entity
     * @param bool $status Availability status
     *
     * @return void
     */
    public function setAvailable(Book $book, bool $status): void;

    /**
     * Create Book cover.
     *
     * @param UploadedFile $uploadedFile Uploaded file representing cover
     * @param Book         $book         Book entity to attach cover to
     *
     * @return void
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createCover(UploadedFile $uploadedFile, Book $book): void;

    /**
     * Update Book cover.
     *
     * @param UploadedFile $uploadedFile Uploaded file representing new cover
     * @param Book         $book         Book entity to update cover for
     *
     * @return void
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updateCover(UploadedFile $uploadedFile, Book $book): void;

    /**
     * Check if Book can be deleted.
     *
     * @param Book $book Book entity to check
     *
     * @return bool True if book can be deleted, false otherwise
     */
    public function canBeDeleted(Book $book): bool;
}
