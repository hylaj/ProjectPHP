<?php
/**
 * Rental controller.
 */

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Rental;
use App\Form\Type\RentalType;
use App\Service\BookServiceInterface;
use App\Service\RentalServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * Class Rental Controller.
 */
#[Route('/rental')]
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
        if (!$this->rentalService->canBeRented($book)){
            $this->addFlash(
                'warning',
                $this->translator->trans('message.book_not_available')
            );
            return $this->redirectToRoute('book_index');
        }

        $rental = new Rental();
        $this->rentalService->setRentalDetails(false, $this->getUser(), $book, $rental);

        $this->bookService->setAvailable($book, false);
        $form=$this->createForm(
            RentalType::class,
            $rental,
        [
            'method' => 'POST',
            'action' => $this->generateUrl('rent', ['id' => $book->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $this->bookService->save($book);
            $this->rentalService->save($rental);

            $this->addFlash(
                'success',
                $this->translator->trans('message.rented_successfully')
            );
            return $this->redirectToRoute(
                'rented_books'
            );
        }
        return $this->render(
            'rental/rent.html.twig',
            [
                'form' => $form->createView()
            ]
        );

    }//end rent()

    #[Route('/{id}/return', name:'return', requirements:['id' => '[1-9]\d*'], methods: 'GET|DELETE' )]
    public function return(Request $request, Rental $rental): Response {

       /* if (!$this->rentalService->canBeReturned($rental)){
            $this->addFlash(
                'warning',
                $this->translator->trans('message.book_cannot_be_returned')
            );
            return $this->redirectToRoute('rented_books');
        }*/

        $form= $this->createForm(
            FormType::class,
            $rental,
        [
            'method' => 'DELETE',
            'action' => $this->generateUrl('return', ['id' => $rental->getId()]),
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->bookService->setAvailable($rental->getBook(), true);
            $this->rentalService->delete($rental);

            $this->addFlash(
                'success',
                $this->translator->trans('message.returned_successfully')
            );

            return $this->redirectToRoute('rented_books');
        }
        return $this->render(
            'rental/return.html.twig',
            [
                'form' => $form->createView(),
                'rental' => $rental,
            ]
        );

}

    #[Route('/list', name: 'rented_books', methods: 'GET')]
    #[IsGranted('ROLE_USER')]
    public function show(#[MapQueryParameter] int $page=1): Response
    {
        $owner=$this->getUser()->getId();
        $pagination = $this->rentalService->getPaginatedListByOwner(
            $page,
            $owner,
        );
        return $this->render('rental/show.html.twig', ['pagination' => $pagination]);

    }//end index()


    #[Route('/pending-approval', name: 'rental_pending_approval', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(#[MapQueryParameter] int $page=1): Response
    {
        $pagination = $this->rentalService->getPaginatedListByStatus(
            $page
        );
        return $this->render('rental/index.html.twig', ['pagination' => $pagination]);

    }//end index()


    #[Route('/{id}/approve', name: 'rental_approve', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('ROLE_ADMIN')]
    public function approve(Rental $rental): Response
    {
        $this->rentalService->setStatus(true, $rental);
        $this->rentalService->save($rental);

        $this->addFlash(
            'success',
            $this->translator->trans('message.approved_successfully')
        );
        return $this->redirectToRoute(
            'rental_pending_approval'
        );
    }

    #[Route('/{id}/deny', name: 'rental_deny', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT|DELETE')]
    #[IsGranted('ROLE_ADMIN')]
    public function deny(Rental $rental): Response
    {
        $this->bookService->setAvailable($rental->getBook(), true);
        $this->bookService->save($rental->getBook());
        $this->rentalService->delete($rental);

        $this->addFlash(
            'success',
            $this->translator->trans('message.denied_successfully')
        );
        return $this->redirectToRoute(
            'rental_pending_approval'
        );
    }





}//end class