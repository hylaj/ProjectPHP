<?php
/**
 * Rating entity.
 */

namespace App\Entity;

use App\Repository\RatingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *Class Rating.
 */
#[ORM\Entity(repositoryClass: RatingRepository::class)]
#[ORM\Table(name: 'ratings')]
class Rating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    private ?int $bookRating = null;

    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type(User::class)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Book::class, fetch: 'EXTRA_LAZY')]
    #[Assert\NotBlank]
    #[Assert\Type(Book::class)]
    private ?Book $book = null;

    /**
     * Getter for Id.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for BookRating..
     *
     * @return int|null
     */
    public function getBookRating(): ?int
    {
        return $this->bookRating;
    }

    /**
     * Setter for BookRating.
     *
     * @param int $bookRating
     * @return void
     */
    public function setBookRating(int $bookRating): void
    {
        $this->bookRating = $bookRating;
    }

    /**
     *  Getter for User.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     *  Setter for User.
     *
     * @param User|null $user
     * @return void
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     *  Getter for Book.
     *
     * @return Book|null
     */
    public function getBook(): ?Book
    {
        return $this->book;
    }

    /**
     *  Setter for Book.
     *
     * @param Book|null $book
     * @return void
     */
    public function setBook(?Book $book): void
    {
        $this->book = $book;
    }
}
