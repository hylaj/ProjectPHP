<?php
/**
 * Rating repository.
 */
namespace App\Repository;

use App\Entity\Book;
use App\Entity\Rating;
use App\Entity\Rental;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class BookRepository.
 *
 * @extends ServiceEntityRepository<Rating>
 */
class RatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rating::class);
    }

    public function save(Rating $rating): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($rating);
        $this->_em->flush();
    }// end save()

    /**
     * Delete entity.
     *
     * @param Rating $rating Rating entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Rating $rating): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($rating);
        $this->_em->flush();
    }// end delete()

    public function findAverageRatingAndCountByBook(int $bookId): ?array
    {
        $result = $this->createQueryBuilder('rating')
            ->select('AVG(rating.bookRating) as avgRating, COUNT(rating) as ratingCount')
            ->where('rating.book = :bookId')
            ->setParameter('bookId', $bookId)
            ->getQuery()
            ->getScalarResult();

        if (isset($result[0])) {
            return [
                'avgRating' => $result[0]['avgRating'],
                'ratingCount' => $result[0]['ratingCount'],
            ];
        } else {
            return null;
        }


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
        return $queryBuilder ?? $this->createQueryBuilder('rating');
    }

    //    /**
    //     * @return Rating[] Returns an array of Rating objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Rating
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
