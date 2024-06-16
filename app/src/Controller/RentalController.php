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
use DateTimeImmutable;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
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
     *
     * @param RentalServiceInterface $rentalService Rental service
     * @param TranslatorInterface    $translator    Translator
     * @param BookServiceInterface   $bookService   Book service
     */
    public function __construct(
        private readonly RentalServiceInterface $rentalService,
        private readonly TranslatorInterface $translator,
        private readonly BookServiceInterface $bookService
    ) {
    }

    /**
     * Rent a book.
     *
     * @param Request $request HTTP Request
     * @param Book    $book    Book entity
     *
     * @return Response HTTP Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route('/{id}/rent', name: 'rent', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    #[IsGranted('RENT', subject: 'book')]
    public function rent(Request $request, Book $book): Response
    {
      /*  if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->rentalService->canBeRented($book)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.book_not_available')
            );

            return $this->redirectToRoute('book_index');
        }*/

        $rental = new Rental();
        $this->rentalService->setRentalDetails(false, $this->getUser(), $book, $rental);

        $this->bookService->setAvailable($book, false);
        $form = $this->createForm(
            RentalType::class,
            $rental,
            [
                'method' => 'POST',
                'action' => $this->generateUrl('rent', ['id' => $book->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bookService->save($book);
            $this->rentalService->save($rental);

            $this->addFlash(
                'success',
                $this->translator->trans('message.rented_successfully')
            );

            return $this->redirectToRoute('rented_books');
        }

        return $this->render(
            'rental/rent.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Return a rented book.
     *
     * @param Request $request HTTP Request
     * @param Rental  $rental  Rental entity
     *
     * @return Response HTTP Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route('/{id}/return', name: 'return', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[IsGranted('RETURN', subject: 'rental')]
    public function return(Request $request, Rental $rental): Response
    {
        /*if ($this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }*/

        $form = $this->createForm(
            FormType::class,
            $rental,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('return', ['id' => $rental->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

    /**
     * List rented books.
     *
     * @param int $page Page number
     *
     * @return Response HTTP Response
     */
    #[Route('/list', name: 'rented_books', methods: 'GET')]
    #[IsGranted('VIEW')]
    public function show(#[MapQueryParameter] int $page = 1): Response
    {
        $today = new \DateTimeImmutable();
        $overdueRentals = $this->rentalService->findOverdueRentalsByUser($this->getUser(), $today);
        if ($overdueRentals){
            foreach ($overdueRentals as $rental) {
                $message = $this->translator->trans(
                    'You have overdue rental with ID %id%. Return date was %date%.',
                    [
                        '%id%' => $rental->getId(),
                        '%date%' => $rental->getReturnDate()->format('Y-m-d')
                    ],
                    'messages'
                );
                $this->addFlash('warning', $message);
            }
        }


        $owner = $this->getUser()->getId();
        $pagination = $this->rentalService->getPaginatedListByOwner(
            $page,
            $owner,
        );

        return $this->render('rental/show.html.twig', ['pagination' => $pagination]);
    }

    /**
     * List rentals pending approval.
     *
     * @param int $page Page number
     *
     * @return Response HTTP Response
     */
    #[Route('/pending-approval', name: 'rental_pending_approval', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    #[IsGranted('VIEW_ALL_RENTALS')]
    public function pending(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->rentalService->getPaginatedListByStatus($page);

        return $this->render('rental/pending.html.twig', ['pagination' => $pagination]);
    }

    /**
     * List all rentals.
     *
     * @param int $page Page number
     *
     * @return Response HTTP Response
     */
    #[Route('/all', name: 'all_rentals', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    #[IsGranted('VIEW_ALL_RENTALS')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->rentalService->getPaginatedList($page);

        return $this->render('rental/index.html.twig', ['pagination' => $pagination]);
    }
    /**
     * List overdue rentals.
     *
     * @param int $page Page number
     *
     * @return Response HTTP Response
     */
    #[Route('/overdue', name: 'overdue_rentals', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    #[IsGranted('VIEW_ALL_RENTALS')]
    public function overdue(#[MapQueryParameter] int $page = 1): Response
    {
        $today = new DateTimeImmutable();
        $pagination = $this->rentalService->getPaginatedListByDate($page, $today);

        return $this->render('rental/overdue.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Approve a rental.
     *
     * @param Rental $rental Rental entity
     *
     * @return Response HTTP Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route('/{id}/approve', name: 'rental_approve', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('APPROVE', subject: 'rental')]
    public function approve(Rental $rental): Response
    {
        $this->rentalService->setStatus(true, $rental);
        $this->rentalService->save($rental);

        $this->addFlash(
            'success',
            $this->translator->trans('message.approved_successfully')
        );

        return $this->redirectToRoute('rental_pending_approval');
    }

    /**
     * Deny a rental.
     *
     * @param Rental $rental Rental entity
     *
     * @return Response HTTP Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route('/{id}/deny', name: 'rental_deny', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT|DELETE')]
    #[IsGranted('DENY', subject: 'rental')]
    public function deny(Rental $rental): Response
    {
        $this->bookService->setAvailable($rental->getBook(), true);
        $this->bookService->save($rental->getBook());
        $this->rentalService->delete($rental);

        $this->addFlash(
            'success',
            $this->translator->trans('message.denied_successfully')
        );

        return $this->redirectToRoute('rental_pending_approval');
    }
}// end class
