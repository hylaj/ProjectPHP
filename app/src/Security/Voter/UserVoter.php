<?php
/**
 * User voter.
 */

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserVoter.
 */
class UserVoter extends Voter
{
    private const EDIT = 'EDIT';
    private const VIEW_USER = 'VIEW_USER';
    private const VIEW_USER_LIST = 'VIEW_USER_LIST';
    private const MANAGE = 'MANAGE';

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
        if (self::VIEW_USER_LIST === $attribute) {
            return true;
        }

        return in_array($attribute, [self::EDIT, self::VIEW_USER, self::MANAGE])
            && $subject instanceof User;
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
        $current_user = $token->getUser();
        if (!$current_user instanceof UserInterface) {
            return false;
        }

        if (self::VIEW_USER_LIST === $attribute) {
            return $this->canViewUserList($current_user);
        }

        if (!$subject instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::VIEW_USER => $this->canViewUser($subject, $current_user),
            self::EDIT => $this->canEdit($subject, $current_user),
            self::MANAGE => $this->canPromote($subject, $current_user),
            default => false,
        };
    }

    /**
     * Checks if user can view his own profile.
     *
     * @param UserInterface $user
     * @param UserInterface $current_user
     * @return bool
     */
    private function canViewUser(UserInterface $user, UserInterface $current_user): bool
    {
        if (in_array('ROLE_ADMIN', $current_user->getRoles())) {
            return true;
        } else {
            return $current_user === $user;
        }
    }

    /**
     * Checks if user can view all users.
     *
     * @param UserInterface $current_user
     * @return bool
     */
    private function canViewUserList(UserInterface $current_user): bool
    {
        return in_array('ROLE_ADMIN', $current_user->getRoles());
    }

    /**
     * Checks if user can edit user details.
     *
     * @param UserInterface $user
     * @param UserInterface $current_user
     * @return bool
     */
    private function canEdit(UserInterface $user, UserInterface $current_user): bool
    {
        if (in_array('ROLE_ADMIN', $current_user->getRoles())) {
            return true;
        } else {
            return in_array('ROLE_USER', $current_user->getRoles()) && ($user === $current_user);
        }
    }

    /**
     * Checks if user can promote other user.
     *
     * @param UserInterface $user
     * @param UserInterface $current_user
     * @return bool
     */
    private function canPromote(UserInterface $user, UserInterface $current_user): bool
    {
        return in_array('ROLE_ADMIN', $current_user->getRoles()) && $current_user !== $user;
    }
}
