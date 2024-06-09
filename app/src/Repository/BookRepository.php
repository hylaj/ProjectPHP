<?php
/**
 * Book repository.
 */

namespace App\Repository;

use App\Dto\BookListFiltersDto;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\User;
use App\Form\DataTransformer\TagsDataTransformer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\DBAL\Types\Types;
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
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry, private readonly TagsDataTransformer $tagsDataTransformer)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(BookListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder= $this->getOrCreateQueryBuilder()
            ->select(
                'partial book.{id, releaseDate, title, author, available, description}',
                'partial category.{id, title}',
                'partial tags.{id, title}'
            )
            ->join('book.category', 'category')
            ->leftJoin('book.tags', 'tags')
            ->orderBy('book.title', 'DESC');

        return $this->applyFiltersToList($queryBuilder, $filters);
    }

    /**
     * @param Category $category
     * @return array
     */
    public function findBooksByCategory(Category $category): array
    {
        return $this->getOrCreateQueryBuilder()
            ->andWhere('book.category= :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getResult();
    }
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
    }

    /**
     * @param Book $book
     *
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Book $book): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($book);
        $this->_em->flush();

    }//end save()

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
    }

    /**
     * Count books by category.
     *
     * @param Category $category Category
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
    }
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

        return $queryBuilder;
    }

    /**
     * Query tasks by author.
     *
     * @param User $user User entity
     *
     * @return QueryBuilder Query builder

    public function queryByAuthor(User $user): QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        $queryBuilder->andWhere('book.itemAuthor = :itemAuthor')
            ->setParameter('itemAuthor', $user);

        return $queryBuilder;
    } */

}
