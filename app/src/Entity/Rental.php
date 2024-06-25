<?php
/**
 * Renal entity.
 */

namespace App\Entity;

use App\Repository\RentalRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *Class Rental.
 */
#[ORM\Entity(repositoryClass: RentalRepository::class)]
#[ORM\Table(name: 'rentals')]
class Rental
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeImmutable $rentalDate = null;

    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type(User::class)]
    private ?User $owner = null;

    #[ORM\OneToOne(targetEntity: Book::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type(Book::class)]
    private ?Book $book = null;

    #[ORM\Column(type: 'boolean')]
    #[Assert\NotNull]
    private ?bool $status = false;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Type('string')]
    private ?string $comment = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $returnDate = null;

    /**
     * Getter for ID.
     *
     * @return int|null ID
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for RentalDate.
     *
     * @return \DateTimeImmutable|null rentalDate
     */
    public function getRentalDate(): ?\DateTimeImmutable
    {
        return $this->rentalDate;
    }

    /**
     * Setter for RentalDate.
     *
     * @param \DateTimeImmutable $rentalDate rentalDate
     *
     * @return void
     */
    public function setRentalDate(\DateTimeImmutable $rentalDate): void
    {
        $this->rentalDate = $rentalDate;
    }

    /**
     * Getter for owner.
     *
     * @return User|null Owner
     */
    public function getOwner(): ?User
    {
        return $this->owner;
    }

    /**
     * Setter for Owner.
     *
     * @param User $owner Owner
     *
     * @return void
     */
    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }

    /**
     *  Getter for book.
     *
     * @return Book|null Book
     */
    public function getBook(): ?Book
    {
        return $this->book;
    }

    /**
     * Setter for Book.
     *
     * @param Book $book Book
     *
     * @return void
     */
    public function setBook(Book $book): void
    {
        $this->book = $book;
    }

    /**
     *  Getter for Status.
     *
     * @return bool|null status
     */
    public function getStatus(): ?bool
    {
        return $this->status;
    }

    /**
     *  Setter for Status.
     *
     * @param bool $status status+
     *
     * @return void
     */
    public function setStatus(bool $status): void
    {
        $this->status = $status;
        if ($status) {
            $this->returnDate = $this->getRentalDate()->modify('+30 days');
        }
    }

    /**
     * Getter for Comment.
     *
     * @return string|null comment
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     *  Setter for Comment.
     *
     * @param string|null $comment comment
     *
     * @return void
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     *  Getter for ReturnDate.
     *
     * @return \DateTimeImmutable|null returnDate
     */
    public function getReturnDate(): ?\DateTimeImmutable
    {
        return $this->returnDate;
    }

    /**
     * Setter for ReturnDate.
     *
     * @param \DateTimeImmutable $returnDate returnDate
     *
     * @return void
     */
    public function setReturnDate(\DateTimeImmutable $returnDate): void
    {
        $this->returnDate = $returnDate;
    }
}
