<?php
/**
 * Book service.
 */

namespace App\Service;

use App\Dto\BookListFiltersDto;
use App\Dto\BookListInputFiltersDto;
use App\Entity\Book;
use App\Entity\Category;
use App\Repository\BookRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @param BookRepository $bookRepository
     * @param PaginatorInterface $paginator
     * @param CategoryServiceInterface $categoryService
     * @param TagServiceInterface $tagService
     * @param FileUploadServiceInterface $fileUploadService
     * @param string $targetDirectory
     * @param Filesystem $filesystem
     */
    public function __construct(private readonly BookRepository $bookRepository, private readonly PaginatorInterface $paginator, private readonly CategoryServiceInterface $categoryService, private readonly TagServiceInterface $tagService, private readonly FileUploadServiceInterface $fileUploadService, private readonly string $targetDirectory, private readonly Filesystem $filesystem) {
    }// end __construct()


    /**
     * Get Paginated List.
     *
     * @param int $page
     * @param BookListInputFiltersDto $filters
     *
     * @return PaginationInterface
     */
    public function getPaginatedList(int $page, BookListInputFiltersDto $filters): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->bookRepository->queryAll($filters),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Get Books By Category.
     *
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
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Book $book): void
    {
        $this->bookRepository->save($book);
    }// end save()


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
    public function delete(Book $book): void
    {
        $this->bookRepository->delete($book);
    }

    /**
     * Sets availability of book.
     *
     * @param Book $book
     * @param bool $status
     *
     * @return void
     */
    public function setAvailable(Book $book, bool $status): void
    {
        $book->setAvailable($status);
    }

    /**
     * Create avatar.
     *
     * @param UploadedFile $uploadedFile
     * @param Book $book
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createCover(UploadedFile $uploadedFile, Book $book): void
    {
        $coverFilename = $this->fileUploadService->upload($uploadedFile);

        $book->setCoverFilename($coverFilename);
        $this->bookRepository->save($book);
    }


    /**
     * Update cover.
     *
     * @param UploadedFile $uploadedFile
     * @param Book $book
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updateCover(UploadedFile $uploadedFile, Book $book): void
    {
        $filename = $book->getCoverFilename();

        if (null !== $filename) {
            $this->filesystem->remove(
                $this->targetDirectory.'/'.$filename
            );
        }
        $this->createCover($uploadedFile, $book);
    }


    /**
     * Can Category be deleted?
     *
     * @param Book $book
     * @return bool
     */
    public function canBeDeleted(Book $book): bool
    {
        try {
            $result = $book->isAvailable();

            return $result > 0;
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
    }
    /**
     *  Prepare filters for the tasks list.
     *
     * @return BookListFiltersDto Result filters
     * @return BookListFiltersDto
     *
     * @throws NonUniqueResultException
     */
    private function prepareFilters(BookListInputFiltersDto $filters): BookListFiltersDto
    {
        return new BookListFiltersDto(
            null !== $filters->categoryId ? $this->categoryService->findOneById($filters->categoryId) : null,
            null !== $filters->tagId ? $this->tagService->findOneById($filters->tagId) : null,
            $filters->titleSearch,
            $filters->authorSearch
        );
    }

}// end class
