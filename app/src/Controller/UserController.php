<?php
/**
 * User Controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserDetailsType;
use App\Form\Type\UserPasswordType;
use App\Form\Type\RegistrationType;
use App\Form\Type\UserRoleType;
use App\Service\UserServiceInterface;
use Cassandra\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 *
 */
#[Route('/user')]
class UserController extends AbstractController
{


    /**
     * Constructor.
     *
     * @param UserServiceInterface $userService User service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(private readonly UserServiceInterface $userService, private readonly TranslatorInterface $translator, private readonly UserPasswordHasherInterface $passwordHasher)
    {

    }//end __construct()


    /**
     * @param  User $user
     * @return Response
     */
    #[Route('/{id}', name: 'user_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    #[IsGranted('VIEW_USER', subject: 'user')]
    public function show(User $user): Response
    {
        return $this->render(
            'user/show.html.twig',
            ['user' => $user]
        );

    }//end show()


    /**
     * @param  User $user
     * @return Response
     */
    #[Route('/list', name: 'user_index', methods: 'GET')]
    #[IsGranted('VIEW_USER_LIST')]
    public function index(#[MapQueryParameter] int $page=1): Response
    {
        $pagination = $this->userService->getPaginatedList(
            $page,
        );
        return $this->render('user/index.html.twig', ['pagination' => $pagination]);

    }//end index()


    /**
     * @param  Request $request
     * @return Response
     */
    #[Route('/{id}/edit-password', name: 'password_edit', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'PUT'])]
    #[IsGranted('EDIT', subject: 'user')]
    public function edit_password(Request $request, User $user): Response
    {
        $form = $this->createForm(
            UserPasswordType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('password_edit', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser() === $user) {
                $currentPassword = $form->get('currentPassword')->getData();

                if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
                    $this->addFlash(
                        'error',
                        $this->translator->trans('message.current_password_invalid')
                    );

                    return $this->redirectToRoute('password_edit', ['id' => $user->getId()]);
                }
            }

            $newPassword    = $form->get('password')->getData();
            $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);
            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );
            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }//end if

        return $this->render(
            'user/edit-password.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );

    }//end edit_password()


    /**
     * @param  Request $request
     * @return Response
     */
    #[Route('/{id}/edit-details', name: 'details_edit', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'PUT'])]
    #[IsGranted('EDIT', subject: 'user')]
    public function edit_details(Request $request, User $user): Response
    {
        $form = $this->createForm(
            UserDetailsType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('details_edit', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user);
            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return $this->render(
            'user/edit-details.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );

    }//end edit_details()


    #[Route('/{id}/promote', name: 'promote_user', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'PUT'])]
    #[IsGranted('MANAGE', subject: 'user')]
    public function promote(Request $request, User $user): Response
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.user_cannot_be_promoted')
            );
            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        $form = $this->createForm(
            FormType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('promote_user', ['id' => $user->getId()]),
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
            $this->userService->save($user);
            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return $this->render(
            'user/promote.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );

    }//end promote()


    #[Route('/{id}/demote', name: 'demote_admin', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'PUT'])]
    #[IsGranted('MANAGE', subject: 'user')]
    public function demote(Request $request, User $user): Response
    {
        if (!in_array('ROLE_ADMIN', $user->getRoles()) || !$this->userService->canBeDemoted('ROLE_ADMIN')) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.user_cannot_be_demoted')
            );
            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        $form = $this->createForm(
            FormType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('demote_admin', ['id' => $user->getId()]),
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']);
            $this->userService->save($user);
            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return $this->render(
            'user/demote.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );

    }//end demote()


    #[Route('/{id}/block', name: 'block_user', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'PUT'])]
    #[IsGranted('MANAGE', subject: 'user')]
    public function block(Request $request, User $user): Response
    {
        if ($user->isBlocked()) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.user_already_blocked')
            );
            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        $form = $this->createForm(
            FormType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('block_user', ['id' => $user->getId()]),
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setBlocked(true);
            $this->userService->save($user);
            $this->addFlash(
                'success',
                $this->translator->trans('message.blocked_successfully')
            );

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return $this->render(
            'user/block.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );

    }//end block()


    #[Route('/{id}/unblock', name: 'unblock_user', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'PUT'])]
    #[IsGranted('MANAGE', subject: 'user')]
    public function unblock(Request $request, User $user): Response
    {
        if (!$user->isBlocked()) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.user_not_blocked')
            );
            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        $form = $this->createForm(
            FormType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('unblock_user', ['id' => $user->getId()]),
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setBlocked(false);
            $this->userService->save($user);
            $this->addFlash(
                'success',
                $this->translator->trans('message.unblocked_successfully')
            );

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return $this->render(
            'user/unblock.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );

    }//end block()


}//end class
