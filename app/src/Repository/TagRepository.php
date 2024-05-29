<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 */
class TagRepository extends ServiceEntityRepository
{


    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);

    }//end __construct()


    // **
    // * @return Tag[] Returns an array of Tag objects
    // */
    // public function findByExampleField($value): array
    // {
    // return $this->createQueryBuilder('t')
    // ->andWhere('t.exampleField = :val')
    // ->setParameter('val', $value)
    // ->orderBy('t.id', 'ASC')
    // ->setMaxResults(10)
    // ->getQuery()
    // ->getResult()
    // ;
    // }
    // public function findOneBySomeField($value): ?Tag
    // {
    // return $this->createQueryBuilder('t')
    // ->andWhere('t.exampleField = :val')
    // ->setParameter('val', $value)
    // ->getQuery()
    // ->getOneOrNullResult()
    // ;
    // }


    /**
     * @param string $title
     * @return Tag|null
     * @throws NonUniqueResultException
     */
    public function findOneByTitle(string $title): ?Tag
    {
        return $this->getOrcreateQueryBuilder()
            ->andWhere('tag.title= :title')
            ->setParameter('title', $title)
            ->getQuery()
            ->getOneOrNullResult();
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
        return $queryBuilder ?? $this->createQueryBuilder('tag');
    }

    /**
     * @param Tag $tag
     *
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Tag $tag): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($tag);
        $this->_em->flush();

    }//end save()

    /**
     * Query all records.
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select('partial tag.{id, createdAt, title, slug, updatedAt}')
            ->orderBy('tag.createdAt', 'DESC');
    }
    /**
     * Delete entity.
     *
     * @param Tag $tag Tag entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Tag $tag): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($tag);
        $this->_em->flush();
    }

}//end class
