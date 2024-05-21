<?php
/**
 * Tag service.
 */

namespace App\Service;

use App\Entity\Book;
use App\Entity\Tag;
use App\Repository\BookRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\PaginatorInterface;

class TagService implements TagServiceInterface
{
    /**
     * Constructor.
     *
     * @param TagRepository     $tagRepository Tag repository
     */
    public function __construct(private readonly TagRepository $tagRepository)
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

}
