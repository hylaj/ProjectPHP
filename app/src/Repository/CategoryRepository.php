<?php
/**
 * Category Repository.
 */

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);

    }//end __construct()


    // **
    // * @return Category[] Returns an array of Category objects
    // */
    // public function findByExampleField($value): array
    // {
    // return $this->createQueryBuilder('c')
    // ->andWhere('c.exampleField = :val')
    // ->setParameter('val', $value)
    // ->orderBy('c.id', 'ASC')
    // ->setMaxResults(10)
    // ->getQuery()
    // ->getResult()
    // ;
    // }


    /**
     * Query all records.
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()->select('partial category.{id, createdAt, title, slug, updatedAt, itemAuthor}')->orderBy('category.createdAt', 'DESC');

    }//end queryAll()


    // public function findOneBySomeField($value): ?Category
    // {
    // return $this->createQueryBuilder('c')
    // ->andWhere('c.exampleField = :val')
    // ->setParameter('val', $value)
    // ->getQuery()
    // ->getOneOrNullResult()
    // ;
    // }


    /**
     * @param  QueryBuilder|null $queryBuilder
     * @return QueryBuilder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $queryBuilder=null): QueryBuilder
    {
        return ($queryBuilder ?? $this->createQueryBuilder('category'));

    }//end getOrCreateQueryBuilder()


    // **
    // * @return Category[] Returns an array of Category objects
    // */
    // public function findByExampleField($value): array
    // {
    // return $this->createQueryBuilder('c')
    // ->andWhere('c.exampleField = :val')
    // ->setParameter('val', $value)
    // ->orderBy('c.id', 'ASC')
    // ->setMaxResults(10)
    // ->getQuery()
    // ->getResult()
    // ;
    // }
    // public function findOneBySomeField($value): ?Category
    // {
    // return $this->createQueryBuilder('c')
    // ->andWhere('c.exampleField = :val')
    // ->setParameter('val', $value)
    // ->getQuery()
    // ->getOneOrNullResult()
    // ;
    // }


    /**
     * @param Category $category
     *
     * @return void
     */
    public function save(Category $category): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($category);
        $this->_em->flush();

    }//end save()

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
    }
}//end class
