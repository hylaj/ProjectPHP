<?php
/**
 * Tag service interface.
 */

namespace App\Service;

use App\Entity\Book;
use App\Entity\Tag;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface BookServiceInterface.
 */
interface TagServiceInterface
{
    /**
     * Find by title.
     *
     * @param string $title Tag title
     *
     * @return Tag|null Tag entity
     */
    public function findOneByTitle(string $title): ?Tag;

    /**
     * Save entity.
     *
     * @param Tag $tag
     */
    public function save(Tag $tag): void;

    /**
     * Get paginated list.
     *
     * @param integer $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;


    /**
     * Delete entity.
     *
     * @param Tag $tag
     *
     * @return void
     */
    public function delete(Tag $tag): void;

}