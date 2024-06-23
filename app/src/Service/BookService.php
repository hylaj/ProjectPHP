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
     * @param BookRepository     $bookRepository Book repository
     * @param PaginatorInterface $paginator      Paginator
     */
    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly PaginatorInterface $paginator,
        private readonly CategoryServiceInterface $categoryService,
        private readonly TagServiceInterface $tagService,
        private readonly FileUploadServiceInterface $fileUploadService,
        private readonly string $targetDirectory,
        private readonly Filesystem $filesystem
    ) {
    }// end __construct()

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
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
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Book $book): void
    {
        $this->bookRepository->delete($book);
    }

    public function setAvailable(Book $book, bool $status): void
    {
        $book->setAvailable($status);
    }

    /**
     * Prepare filters for the tasks list.
     *
     * @param BookListInputFiltersDto $filters Raw filters from request
     *
     * @return BookListFiltersDto Result filters
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

    /**
     * Create avatar.
     */
    public function createCover(UploadedFile $uploadedFile, Book $book): void
    {
        $coverFilename = $this->fileUploadService->upload($uploadedFile);

        $book->setCoverFilename($coverFilename);
        $this->bookRepository->save($book);
    }

    /**
     * Update cover.
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
}// end class
