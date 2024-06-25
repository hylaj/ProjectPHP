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
     * @param Category $category
     *
     * @return array
     */
    public function getBooksByCategory(Category $category): array;


    /**
     * Save entity.
     *
     * @param Book $book Book entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Book $book): void;

    /**
     * Delete entity.
     *
     * @param Book $book
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
     * @param Book $book
     * @param bool $status
     *
     * @return void
     */
    public function setAvailable(Book $book, bool $status): void;

    /**
     * Create avatar.
     *
     * @param UploadedFile $uploadedFile
     * @param Book         $book
     *
     * @return void
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createCover(UploadedFile $uploadedFile, Book $book): void;

    /**
     * Update cover.
     *
     * @param UploadedFile $uploadedFile
     * @param Book         $book
     *
     * @return void
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updateCover(UploadedFile $uploadedFile, Book $book): void;

    /**
     * Can Category be deleted?
     *
     * @param Book $book
     *
     * @return bool
     */
    public function canBeDeleted(Book $book): bool;
}
