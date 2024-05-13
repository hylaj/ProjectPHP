<?php
/**
 * Category Repository.
 */

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);

    }// end __construct()

//    /**
//     * @return Category[] Returns an array of Category objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
    /**
     * Query all records.
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
public function queryAll(): QueryBuilder
{
    return $this->getOrCreateQueryBuilder()
        ->select('category')
        ->orderBy('category.createdAt', 'DESC');
}

//    public function findOneBySomeField($value): ?Category
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

/**
 * @param QueryBuilder|null $queryBuilder
 * @return QueryBuilder
 */
private function getOrCreateQueryBuilder(?QueryBuilder $queryBuilder = null): QueryBuilder
{
    return $queryBuilder ?? $this->createQueryBuilder('category');
}
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
}// end class