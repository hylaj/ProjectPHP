<?php
/**
 * Rental voter.
 */

namespace App\Security\Voter;

use App\Entity\Book;
use App\Entity\Rating;
use App\Entity\Rental;
use App\Entity\User;
use App\Repository\RatingRepository;
use App\Service\RentalServiceInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class RentalVoter.
 *
 */

class RentalVoter extends Voter
{
    /**
     * Create permission.
     *
     * @const string
     */
    private const RENT = 'RENT';

    /**
     * Create permission.
     *
     * @const string
     */
    private const RETURN = 'RETURN';

    /**
     * Create permission.
     *
     * @const string
     */
    private const APPROVE = 'APPROVE';

    /**
     * Create permission.
     *
     * @const string
     */
    private const VIEW = 'VIEW';

    /**
     * Create permission.
     *
     * @const string
     */
    private const VIEW_ALL_RENTALS = 'VIEW_ALL_RENTALS';

    /**
     * Create permission.
     *
     * @const string
     */
    private const DENY = 'DENY';

    public function __construct( private readonly RentalServiceInterface $rentalService)
    {

    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($subject instanceof Book) {
            return self::RENT === $attribute;
        }

        if ($subject instanceof Rental) {
            return in_array($attribute, [self::RETURN, self::APPROVE, self::DENY]);
        }

        return in_array($attribute, [self::VIEW, self::VIEW_ALL_RENTALS]);
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

        if (self::RENT === $attribute) {
            if (!$subject instanceof Book) {
                return false;
            }
            return $this->canRent($subject, $user);
        }

        if (in_array($attribute, [self::RETURN, self::APPROVE, self::DENY])) {
            if (!$subject instanceof Rental) {
                return false;
            }
            return match ($attribute){
                self::RETURN => $this->canReturn($subject, $user),
                self::APPROVE => $this->canApprove($subject, $user),
                self::DENY => $this->canDeny($subject, $user)
            };
        }
        return match ($attribute) {
            self::VIEW => $this->canView($user),
            self::VIEW_ALL_RENTALS => $this->canViewAllRentals($user),
            default => false,
        };
    }

    private function canRent(Book $book, UserInterface $user): bool
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return false;
        } else {
            return in_array('ROLE_USER', $user->getRoles()) && ($this->rentalService->canBeRented($book));
        }
    }

    private function canReturn(Rental $rental, UserInterface $user): bool
    {
        return ($user->getId()===$rental->getOwner()->getId());

    }
    private function canApprove(Rental $rental, UserInterface $user): bool
    {
        return (in_array('ROLE_ADMIN', $user->getRoles())) && ($rental->getStatus()===false);

    }

    private function canDeny(Rental $rental, UserInterface $user): bool
    {
        return (in_array('ROLE_ADMIN', $user->getRoles())) && ($rental->getStatus()===false);

    }

    private function canView(UserInterface $user): bool
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return false;
        } else {
            return in_array('ROLE_USER', $user->getRoles());
        }

    }
    private function canViewAllRentals(UserInterface $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
    }
}