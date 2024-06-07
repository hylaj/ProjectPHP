<?php
/**
 * User Controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\PasswordType;
use App\Form\Type\UserDetailsType;
use App\Form\Type\UserPasswordType;
use App\Form\Type\RegistrationType;
use App\Service\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    #[Route(
        '/{id}',
        name: 'user_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(User $user): Response
    {
        return $this->render(
            'user/show.html.twig',
            ['user' => $user]
        );

    }//end show()


    /**
     * @param  Request                     $request
     * @param  UserPasswordHasherInterface $passwordHasher
     * @param  EntityManagerInterface      $entityManager
     * @param  TranslatorInterface         $translator
     * @return Response
     */
    #[Route('/{id}/edit-password', name: 'password_edit', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit_password(Request $request): Response
    {
        $user = $this->getUser();

        if ($user->getId() != $request->get('id')) {
            throw $this->createAccessDeniedException('You cannot edit the password of another user.');
            /*
                $this->addFlash(
                'danger',
                $this->translator->trans('message.password_of_another_user')
                );
            return $this->redirectToRoute('password_edit', ['id' => $user->getId()]);*/
        }

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
            $currentPassword = $form->get('currentPassword')->getData();
            if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash(
                    'error',
                    $this->translator->trans('message.current_password_invalid')
                );

                return $this->redirectToRoute('password_edit', ['id' => $user->getId()]);
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
     * @param  Request                     $request
     * @param  UserPasswordHasherInterface $passwordHasher
     * @param  EntityManagerInterface      $entityManager
     * @param  TranslatorInterface         $translator
     * @return Response
     */
    #[Route('/{id}/edit-details', name: 'details_edit', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit_details(Request $request): Response
    {
        $user = $this->getUser();

        if ($user->getId() != $request->get('id')) {
            throw $this->createAccessDeniedException('You cannot edit the details of another user.');
        }

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





}//end class
