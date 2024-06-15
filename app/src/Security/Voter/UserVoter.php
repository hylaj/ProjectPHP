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
 * Class TaskVoter.
 */
class UserVoter extends Voter
{
    private const EDIT = 'EDIT';
    private const VIEW_USER = 'VIEW_USER';
    private const VIEW_USER_LIST = 'VIEW_USER_LIST';
    private const MANAGE = 'MANAGE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (self::VIEW_USER_LIST === $attribute) {
            return true;
        }

        return in_array($attribute, [self::EDIT, self::VIEW_USER, self::MANAGE])
            && $subject instanceof User;
    }

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

    private function canViewUser(UserInterface $user, UserInterface $current_user): bool
    {
        if (in_array('ROLE_ADMIN', $current_user->getRoles())) {
            return true;
        } else {
            return $current_user === $user;
        }
    }

    private function canViewUserList(UserInterface $current_user): bool
    {
        return in_array('ROLE_ADMIN', $current_user->getRoles());
    }

    private function canEdit(UserInterface $user, UserInterface $current_user): bool
    {
        if (in_array('ROLE_ADMIN', $current_user->getRoles())) {
            return true;
        } else {
            return in_array('ROLE_USER', $current_user->getRoles()) && ($user === $current_user);
        }
    }

    private function canPromote(UserInterface $user, UserInterface $current_user): bool
    {
        return in_array('ROLE_ADMIN', $current_user->getRoles()) && $current_user !== $user;
    }
}
