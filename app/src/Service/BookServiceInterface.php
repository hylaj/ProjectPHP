<?php
/**
 * Book service interface.
 */

namespace App\Service;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface BookServiceInterface.
 */
interface BookServiceInterface
{

    /**
     * @param Category $category
     * @return array
     */
    public function getBooksByCategory(Category $category): array;

    /**
     * Save entity.
     *
     * @param Book $book
     */
    public function save(Book $book): void;

    /**
     * Delete entity.
     *
     * @param Book $book
     * @return void
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
    public function getPaginatedList(int $page/*, User $author*/): PaginationInterface;

}
