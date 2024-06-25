<?php
/**
 * Registration Controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegistrationType;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class RegistrationController.
 */
class RegistrationController extends AbstractController
{

    /**
     * Constructor.
     *
     * @param UserServiceInterface        $userService    User service
     * @param TranslatorInterface         $translator     Translator
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     */
    public function __construct(private readonly UserServiceInterface $userService, private readonly TranslatorInterface $translator, private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }// end __construct()

    /**
     * Register action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/register', name: 'register', methods: ['GET', 'POST'])]
    public function register(Request $request): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->addFlash(
                'error',
                $this->translator->trans('message.already_logged_in')
            );

            return $this->redirectToRoute('app_logout');
        }

        $user = new User();

        $user->setRoles(['ROLE_USER']);

        $form = $this->createForm(
            RegistrationType::class,
            $user
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);
            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'register/index.html.twig',
            ['form' => $form->createView()]
        );
    }// end register()
}// end class
