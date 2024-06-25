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
    /**
     * Edit permission.
     *
     * @const string
     */
    private const EDIT = 'EDIT';

    /**
     * View user permission.
     *
     * @const string
     */
    private const VIEW_USER = 'VIEW_USER';

    /**
     * View user list permission.
     *
     * @const string
     */
    private const VIEW_USER_LIST = 'VIEW_USER_LIST';

    /**
     * Manage permission.
     *
     * @const string
     */
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
        $currentUser = $token->getUser();
        if (!$currentUser instanceof UserInterface) {
            return false;
        }

        if (self::VIEW_USER_LIST === $attribute) {
            return $this->canViewUserList($currentUser);
        }

        if (!$subject instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::VIEW_USER => $this->canViewUser($subject, $currentUser),
            self::EDIT => $this->canEdit($subject, $currentUser),
            self::MANAGE => $this->canPromote($subject, $currentUser),
            default => false,
        };
    }

    /**
     * Checks if user can view another user's profile.
     *
     * @param UserInterface $user        The user whose profile is being viewed
     * @param UserInterface $currentUser The current user
     *
     * @return bool Result
     */
    private function canViewUser(UserInterface $user, UserInterface $currentUser): bool
    {
        if (in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return true;
        }

        return $currentUser === $user;
    }

    /**
     * Checks if user can view the user list.
     *
     * @param UserInterface $currentUser The current user
     *
     * @return bool Result
     */
    private function canViewUserList(UserInterface $currentUser): bool
    {
        return in_array('ROLE_ADMIN', $currentUser->getRoles());
    }

    /**
     * Checks if user can edit another user's details.
     *
     * @param UserInterface $user        The user whose details are being edited
     * @param UserInterface $currentUser The current user
     *
     * @return bool Result
     */
    private function canEdit(UserInterface $user, UserInterface $currentUser): bool
    {
        if (in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return true;
        }

        return in_array('ROLE_USER', $currentUser->getRoles()) && ($user === $currentUser);
    }

    /**
     * Checks if user can promote another user.
     *
     * @param UserInterface $user        The user to be promoted
     * @param UserInterface $currentUser The current user
     *
     * @return bool Result
     */
    private function canPromote(UserInterface $user, UserInterface $currentUser): bool
    {
        return in_array('ROLE_ADMIN', $currentUser->getRoles()) && $currentUser !== $user;
    }
}
