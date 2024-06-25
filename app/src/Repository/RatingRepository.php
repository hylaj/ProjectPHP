<?php
/**
 * Rating repository.
 */

namespace App\Repository;

use App\Entity\Rating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class RatingRepository.
 *
 * @extends ServiceEntityRepository<Rating>
 */
class RatingRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rating::class);
    }// end __construct()

    /**
     * Save entity.
     *
     * @param Rating $rating Rating entity
     *
     * @return void
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
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
     * @return void
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

    /**
     * Find average rating and count ratings by book.
     *
     * @param int $bookId Book ID
     *
     * @return array|null Average rating and count
     */
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
        }

        return null;
    }// end findAverageRatingAndCountByBook()

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
    }// end getOrCreateQueryBuilder()
}// end class
