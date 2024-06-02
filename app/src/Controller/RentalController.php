<?php
/**
 * Rental controller.
 */

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Rental;
use App\Service\BookServiceInterface;
use App\Service\RentalServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * Class Rental Controller.
 */

class RentalController extends AbstractController
{


    /**
     * Constructor.
     */
    public function __construct(private readonly RentalServiceInterface $rentalService, private readonly TranslatorInterface $translator, private readonly BookServiceInterface $bookService)
    {

    }//end __construct()


    /**
     * @param  Request $request
     * @param  Book    $book
     * @return Response
     */
    #[Route('/{id}/rent', name: 'rent', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    #[IsGranted('ROLE_USER')]
    public function rent(Request $request, Book $book): Response
    {
        $rental = new Rental();

        $user = $this->getUser();
        $rental->setOwner($user);

        $rental->setBook($book);
        $rental->setStatus(false);

        $book->setAvailable(false);

        $this->rentalService->save($rental);
        $this->bookService->save($book);

        $this->addFlash(
            'success',
            $this->translator->trans('message.rented_successfully')
        );

        $id = $request->get('id');

        return $this->redirectToRoute(
            'book_show',
            ['id' => $id]
        );

    }//end rent()


    #[Route('/rent-index', name: 'rent_index', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(#[MapQueryParameter] int $page=1): Response
    {
        $pagination = $this->rentalService->getPaginatedListByStatus(
            $page
        );
        return $this->render('rental/index.html.twig', ['pagination' => $pagination]);

    }//end index()

    #[Route('/{id}/rent-approve', name: 'rent_approve', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('ROLE_ADMIN')]
    public function approve(Request $request, Rental $rental): Response
    {
        $user = $rental->getOwner();

        $rental->setStatus(true);
        $book = $rental->getBook();
        $book->setItemAuthor($user);
        $this->rentalService->save($rental);
        $this->bookService->save($book);

        $this->addFlash(
            'success',
            $this->translator->trans('message.approved_successfully')
        );
        return $this->redirectToRoute(
            'rent_index'
        );
    }

    #[Route('/{id}/rent-deny', name: 'rent_deny', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT|DELETE')]
    #[IsGranted('ROLE_ADMIN')]
    public function deny(Request $request, Rental $rental): Response
    {
        $book = $rental->getBook();

        $book->setAvailable(true);
        $this->rentalService->delete($rental);
        $this->bookService->save($book);

        $this->addFlash(
            'success',
            $this->translator->trans('message.denied_successfully')
        );
        return $this->redirectToRoute(
            'rent_index'
        );
    }

}//end class
