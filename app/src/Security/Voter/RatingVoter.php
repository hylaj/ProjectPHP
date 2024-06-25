<?php
/**
 * Rating voter.
 */

namespace App\Security\Voter;

use App\Entity\Book;
use App\Entity\Rating;
use App\Repository\RatingRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class RatingVoter.
 */
class RatingVoter extends Voter
{
    /**
     * Create permission.
     *
     * @const string
     */
    private const RATE = 'RATE';

    /**
     * Edit permission.
     *
     * @const string
     */
    private const EDIT = 'EDIT';

    /**
     * Delete permission.
     *
     * @const string
     */
    private const DELETE = 'DELETE';

    /**
     * Constructor.
     *
     * @param RatingRepository $ratingRepository Rating repository
     */
    public function __construct(private readonly RatingRepository $ratingRepository)
    {
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool Result
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($subject instanceof Book) {
            return self::RATE === $attribute;
        }

        return in_array($attribute, [self::DELETE, self::EDIT])
            && $subject instanceof Rating;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string         $attribute Permission name
     * @param mixed          $subject   Object
     * @param TokenInterface $token     Security token
     *
     * @return bool Vote result
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if (self::RATE === $attribute) {
            if (!$subject instanceof Book) {
                return false;
            }

            return $this->canRate($subject, $user);
        }

        if (!$subject instanceof Rating) {
            return false;
        }

        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            default => false,
        };
    }

    /**
     * Checks if user can delete rating.
     *
     * @param Rating        $rating Rating entity
     * @param UserInterface $user   User entity
     *
     * @return bool Result
     */
    private function canDelete(Rating $rating, UserInterface $user): bool
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return false;
        }

        return in_array('ROLE_USER', $user->getRoles()) && ($rating->getUser() === $user);
    }

    /**
     * Checks if user can edit the rating.
     *
     * @param Rating        $rating Rating entity
     * @param UserInterface $user   User entity
     *
     * @return bool Result
     */
    private function canEdit(Rating $rating, UserInterface $user): bool
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return false;
        }

        return in_array('ROLE_USER', $user->getRoles()) && ($rating->getUser() === $user);
    }

    /**
     * Checks if user can rate a book.
     *
     * @param Book          $book Book entity
     * @param UserInterface $user User entity
     *
     * @return bool Result
     */
    private function canRate(Book $book, UserInterface $user): bool
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return false;
        }

        return in_array('ROLE_USER', $user->getRoles()) && null === $this->ratingRepository->findOneBy(['book' => $book, 'user' => $user]);
    }
}
