<?php
/**
 * Book repository.
 */

namespace App\Repository;

use App\Dto\BookListFiltersDto;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Tag;
use App\Form\DataTransformer\TagsDataTransformer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class BookRepository.
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Book>
 *
 * @psalm-suppress LessSpecificImplementedReturnType
 */
class BookRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry     $registry            Manager registry
     * @param TagsDataTransformer $tagsDataTransformer Tags data transformer
     */
    public function __construct(ManagerRegistry $registry, private readonly TagsDataTransformer $tagsDataTransformer)
    {
        parent::__construct($registry, Book::class);
    }// end __construct()

    /**
     * Query all records.
     *
     * @param BookListFiltersDto $filters Filters
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(BookListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->getOrCreateQueryBuilder()
            ->select(
                'partial book.{id, releaseDate, title, author, available, description, coverFilename}',
                'partial category.{id, title}',
                'partial tags.{id, title}'
            )
            ->join('book.category', 'category')
            ->leftJoin('book.tags', 'tags')
            ->orderBy('book.title', 'DESC');

        return $this->applyFiltersToList($queryBuilder, $filters);
    }// end queryAll()

    /**
     * Find books by category.
     *
     * @param Category $category Category entity
     *
     * @return array Array of books
     */
    public function findBooksByCategory(Category $category): array
    {
        return $this->getOrCreateQueryBuilder()
            ->andWhere('book.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getResult();
    }// end findBooksByCategory()

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
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($book);
        $this->_em->flush();
    }// end save()

    /**
     * Delete entity.
     *
     * @param Book $book Book entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Book $book): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($book);
        $this->_em->flush();
    }// end delete()

    /**
     * Count books by category.
     *
     * @param Category $category Category entity
     *
     * @return int Number of books in category
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByCategory(Category $category): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('book.id'))
            ->where('book.category = :category')
            ->setParameter(':category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }// end countByCategory()

    /**
     * Apply filters to paginated list.
     *
     * @param QueryBuilder       $queryBuilder Query builder
     * @param BookListFiltersDto $filters      Filters
     *
     * @return QueryBuilder Query builder
     */
    private function applyFiltersToList(QueryBuilder $queryBuilder, BookListFiltersDto $filters): QueryBuilder
    {
        if ($filters->category instanceof Category) {
            $queryBuilder->andWhere('category = :category')
                ->setParameter('category', $filters->category);
        }

        if ($filters->tag instanceof Tag) {
            $queryBuilder->andWhere('tags IN (:tag)')
                ->setParameter('tag', $filters->tag);
        }

        if ($filters->title) {
            $queryBuilder
                ->andWhere('book.title LIKE :title')
                ->setParameter('title', '%'.$filters->title.'%');
        }

        if (isset($filters->author)) {
            $queryBuilder
                ->andWhere('book.author LIKE :author')
                ->setParameter('author', '%'.$filters->author.'%');
        }

        return $queryBuilder;
    }// end applyFiltersToList()

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('book');
    }// end getOrCreateQueryBuilder()
}// end class
