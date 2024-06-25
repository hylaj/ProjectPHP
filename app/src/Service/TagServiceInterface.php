<?php
/**
 * Tag service interface.
 */

namespace App\Service;

use App\Entity\Tag;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface TagServiceInterface.
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
     * @return void
     */
    public function save(Tag $tag): void;

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;


    /**
     * Delete entity.
     *
     * @param Tag $tag
     * @return void
     */
    public function delete(Tag $tag): void;

    /**
     * Find by id.
     *
     * @param int $id Tag id
     *
     * @return Tag|null Tag entity
     *
     * @throws NonUniqueResultException
     */
    public function findOneById(int $id): ?Tag;
}
