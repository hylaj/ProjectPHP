<?php
/**
 * Book service.
 */

namespace App\Service;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Rector\Symfony\Contract\Tag\TagInterface;

/**
 * Class BookService.
 */
class BookService implements BookServiceInterface
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;


    /**
     * Constructor.
     *
     * @param BookRepository     $bookRepository Book repository
     * @param PaginatorInterface $paginator      Paginator
     */
    public function __construct(private readonly BookRepository $bookRepository, private readonly PaginatorInterface $paginator)
    {

    }//end __construct()


    /**
     * Get paginated list.
     *
     * @param int  $page   Page number
     * @param User $author Author
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page/*, User $author*/): PaginationInterface
    {
        return $this->paginator->paginate(
            //$this->bookRepository->queryByAuthor($author),
            $this->bookRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * @param Category $category
     * @return array
     */
    public function getBooksByCategory(Category $category): array
    {
        return $this->bookRepository->findBooksByCategory($category);
    }

    /**
     * Save entity.
     *
     * @param Book $book Book entity
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Book $book): void
    {
        $this->bookRepository->save($book);

    }//end save()

    /**
     * Delete entity.
     *
     * @param Book $book
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Book $book): void
    {
        $this->bookRepository->delete($book);
    }

    public function setAvailable(Book $book, bool $available): void{
        $book->setAvailable($available);
    }
}//end class
