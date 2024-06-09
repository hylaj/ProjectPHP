<?php
/**
 * Tag service.
 */

namespace App\Service;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Tag;
use App\Repository\BookRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 *Class TagService.
 */
class TagService implements TagServiceInterface
{
    private const PAGINATOR_ITEMS_PER_PAGE = 10;
    /**
     * Constructor.
     *
     * @param TagRepository     $tagRepository Tag repository
     */
    public function __construct(private readonly TagRepository $tagRepository, private readonly PaginatorInterface $paginator)
    {

    }

    /**
     * Find by title.
     *
     * @param string $title Tag title
     *
     * @return Tag|null Tag entity
     */
    public function findOneByTitle(string $title): ?Tag
    {
        return $this->tagRepository->findOneByTitle($title);
    }
    /**
     * Save entity.
     *
     * @param Tag $tag Tag entity
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Tag $tag): void
    {
        $this->tagRepository->save($tag);

    }//end save()

    /**
     * Get paginated list.
     *
     * @param integer $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->tagRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );

    }
    /**
     * Delete entity.
     *
     * @param Tag $tag
     *
     * @return void
     */
    public function delete(Tag $tag): void
    {
        $this->tagRepository->delete($tag);
    }
    /**
     * Find by id.
     *
     * @param int $id Tag id
     *
     * @return Tag|null Tag entity
     *
     * @throws NonUniqueResultException
     */
    public function findOneById(int $id): ?Tag
    {
        return $this->tagRepository->findOneById($id);
    }

}
