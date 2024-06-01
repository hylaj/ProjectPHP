<?php
/**
 * User Controller.
 */

namespace App\Controller;

use App\Form\Type\UserType;
use App\Service\CategoryServiceInterface;
use App\Service\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
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
     * @param TranslatorInterface      $translator  Translator
     */
    public function __construct(private readonly UserServiceInterface $userService, private readonly TranslatorInterface $translator)
    {
    }


    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/{id}/edit-password', name: 'password_edit',requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'PUT'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator): Response
    {
        $user=$this->getUser();

        $form=$this->createForm(
            UserType::class,
            $user,
        [
            'method'=>'PUT',
            'action' => $this->generateUrl('password_edit', ['id' => $user->getId()])
        ]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $currentPassword = $form->get('currentPassword')->getData();
            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash(
                    'error',
                    $this->translator->trans('message.current_password_invalid')
                );

                return $this->redirectToRoute('password_edit', ['id' => $user->getId()]);
            }

            $newPassword= $form->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $this->userService->upgradePassword($user, $hashedPassword);


            $this->addFlash(
                'success',
                $translator->trans('message.edited_successfully')
            );
            return $this->redirectToRoute('book_index');
        }
        return $this->render(
            'user/edit-password.html.twig',
            [
                'form'=> $form->createView(),
                'user' => $user
            ]);

    }
}