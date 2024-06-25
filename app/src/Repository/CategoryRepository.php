<?php
/**
 * Category Repository.
 */

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * CategoryRepository.
 *
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }// end __construct()

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select('partial category.{id, createdAt, title, slug, updatedAt, itemAuthor}')
            ->orderBy('category.createdAt', 'DESC');
    }// end queryAll()

    /**
     * Save entity.
     *
     * @param Category $category Category entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Category $category): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($category);
        $this->_em->flush();
    }// end save()

    /**
     * Delete entity.
     *
     * @param Category $category Category entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Category $category): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($category);
        $this->_em->flush();
    }// end delete()

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('category');
    }// end getOrCreateQueryBuilder()
}// end class
