<?php
/**
 * Rating entity.
 */

namespace App\Entity;

use App\Repository\RatingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Rating.
 */
#[ORM\Entity(repositoryClass: RatingRepository::class)]
#[ORM\Table(name: 'ratings')]
class Rating
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Book rating.
     */
    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    private ?int $bookRating = null;

    /**
     * User.
     */
    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type(User::class)]
    private ?User $user = null;

    /**
     * Book.
     */
    #[ORM\ManyToOne(targetEntity: Book::class, fetch: 'EXTRA_LAZY')]
    #[Assert\NotBlank]
    #[Assert\Type(Book::class)]
    private ?Book $book = null;

    /**
     * Getter for Id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for book rating.
     *
     * @return int|null Book rating
     */
    public function getBookRating(): ?int
    {
        return $this->bookRating;
    }

    /**
     * Setter for book rating.
     *
     * @param int $bookRating Book rating
     */
    public function setBookRating(int $bookRating): void
    {
        $this->bookRating = $bookRating;
    }

    /**
     * Getter for user.
     *
     * @return User|null User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Setter for user.
     *
     * @param User|null $user User
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * Getter for book.
     *
     * @return Book|null Book
     */
    public function getBook(): ?Book
    {
        return $this->book;
    }

    /**
     * Setter for book.
     *
     * @param Book|null $book Book
     */
    public function setBook(?Book $book): void
    {
        $this->book = $book;
    }
}
