<?php
/**
 * Tag voter.
 */

namespace App\Security\Voter;

use App\Entity\Tag;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TaskVoter.
 */
class TagVoter extends Voter
{
    /**
     * Edit permission.
     *
     * @const string
     */
    private const EDIT = 'EDIT';

    /*
     * View permission.
     *
     * @const string

    private const VIEW = 'VIEW';
*/

    /**
     * Delete permission.
     *
     * @const string
     */
    private const DELETE = 'DELETE';

    /**
     * Create permission.
     *
     * @const string
     */
    private const CREATE = 'CREATE';

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
        if (self::CREATE === $attribute) {
            return true;
        }

        return in_array($attribute, [self::EDIT/* , self::VIEW */, self::DELETE])
            && $subject instanceof Tag;
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

        if (self::CREATE === $attribute) {
            return $this->canCreate($user);
        }

        if (!$subject instanceof Tag) {
            return false;
        }

        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            // self::VIEW => $this->canView($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            default => false,
        };
    }

    /**
     * Checks if user can edit tag.
     *
     * @param Tag           $tag  Tag entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canEdit(Tag $tag, UserInterface $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
        //  return $tag->getItemAuthor() === $user;
    }

    /*
     * Checks if user can view tag.
     *
     * @param Tag          $tag Tag entity
     * @param UserInterface $user User
     *
     * @return bool Result

    private function canView(Tag $tag, UserInterface $user): bool
     {
         return $tag->getItemAuthor() === $user;
     }
 */

    /**
     * Checks if user can delete tag.
     *
     * @param Tag           $tag  Tag entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canDelete(Tag $tag, UserInterface $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
        // return return (($this->security->isGranted('ROLE_ADMIN')) && ($tag->getItemAuthor() === $user));
    }

    /**
     * Checks if user can create tag.
     *
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canCreate(UserInterface $user): bool
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
    }
}
