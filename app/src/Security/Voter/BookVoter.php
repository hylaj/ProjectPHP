<?php
/**
 * Book voter.
 */

namespace App\Security\Voter;

use App\Entity\Book;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TaskVoter.
 */
class BookVoter extends Voter
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
        if ($attribute === self::CREATE) {
            return true;
        }
        return in_array($attribute, [self::EDIT/*, self::VIEW*/, self::DELETE])
            && $subject instanceof Book;
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

        if ($attribute === self::CREATE) {
            return $this->canCreate($user);
        }

        if (!$subject instanceof Book) {
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
     * Checks if user can edit book.
     *
     * @param Book          $book Book entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canEdit(Book $book, UserInterface $user): bool
    {
        return (in_array('ROLE_ADMIN', $user->getRoles()));
      //  return $book->getItemAuthor() === $user;
    }


   /*
    * Checks if user can view book.
    *
    * @param Book          $book Book entity
    * @param UserInterface $user User
    *
    * @return bool Result

   private function canView(Book $book, UserInterface $user): bool
    {
        return $book->getItemAuthor() === $user;
    }
*/

    /**
     * Checks if user can delete book.
     *
     * @param Book          $book Book entity
     * @param UserInterface $user User
     *
     * @return bool Result
     */

    private function canDelete(Book $book, UserInterface $user): bool
    {
        return (in_array('ROLE_ADMIN', $user->getRoles()));
        //return return (($this->security->isGranted('ROLE_ADMIN')) && ($book->getItemAuthor() === $user));
    }

    /**
     * Checks if user can create book.
     *
     * @param UserInterface $user User
     *
     * @return bool Result
     */
    private function canCreate(UserInterface $user): bool
    {
        return (in_array('ROLE_ADMIN', $user->getRoles()));
    }
}
