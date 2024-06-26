<?php
/**
 * Book controller.
 */

namespace App\Controller;

use App\Dto\BookListInputFiltersDto;
use App\Entity\Book;
use App\Form\Type\BookType;
use App\Form\Type\SearchType;
use App\Resolver\BookListInputFiltersDtoResolver;
use App\Service\BookServiceInterface;
use App\Service\RatingServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class BookController.
 */
#[Route('/book')]
class BookController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param BookServiceInterface   $bookService   Book service
     * @param TranslatorInterface    $translator    Translator
     * @param RatingServiceInterface $ratingService Rating service
     */
    public function __construct(private readonly BookServiceInterface $bookService, private readonly TranslatorInterface $translator, private readonly RatingServiceInterface $ratingService)
    {
    }

    /**
     * Index action.
     *
     * @param Request                 $request HTTP request
     * @param BookListInputFiltersDto $filters Input filters for book list
     * @param int                     $page    Current page number
     *
     * @return Response HTTP response
     */
    #[Route(name: 'book_index', methods: 'GET')]
    public function index(Request $request, #[MapQueryString(resolver: BookListInputFiltersDtoResolver::class)] BookListInputFiltersDto $filters, #[MapQueryParameter] int $page = 1): Response
    {
        $form = $this->createForm(
            SearchType::class,
            [
                'method' => 'GET',
            ]
        );
        $form->handleRequest($request);

        $pagination = $this->bookService->getPaginatedList(
            $page,
            $filters,
        );

        return $this->render(
            'book/index.html.twig',
            [
                'pagination' => $pagination,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Show action.
     *
     * @param Book $book Book entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'book_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET',
    )]
    #[IsGranted('ROLE_USER')]
    public function show(Book $book): Response
    {
        $rating = $this->ratingService->getRatingByUserAndBook($this->getUser(), $book);
        $ratingsInfo = $this->ratingService->findAverageRatingAndCountByBook($book->getId());

        return $this->render(
            'book/show.html.twig',
            [
                'book' => $book,
                'averageRating' => $ratingsInfo['avgRating'] ?? null,
                'ratingCount' => $ratingsInfo['ratingCount'] ?? null,
                'rating' => $rating,
            ]
        );
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        '/create',
        name: 'book_create',
        methods: 'GET|POST',
    )]
    #[IsGranted('CREATE')]
    public function create(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(
            BookType::class,
            $book
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            if ($file) {
                $this->bookService->createCover($file, $book);
            }
            $this->bookService->save($book);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('book_index');
        }

        return $this->render(
            'book/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Book    $book    Book entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'book_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('EDIT', subject: 'book')]
    public function edit(Request $request, Book $book): Response
    {
        $form = $this->createForm(
            BookType::class,
            $book,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('book_edit', ['id' => $book->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            if ($file) {
                $this->bookService->updateCover($file, $book);
            }

            $this->bookService->save($book);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('book_index');
        }

        return $this->render(
            'book/edit.html.twig',
            [
                'form' => $form->createView(),
                'book' => $book,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Book    $book    Book entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'book_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[IsGranted('DELETE', subject: 'book')]
    public function delete(Request $request, Book $book): Response
    {
        if (!$this->bookService->canBeDeleted($book)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.book_rented_cannot_be_deleted')
            );

            return $this->redirectToRoute('book_index');
        }

        $form = $this->createForm(FormType::class, $book, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('book_delete', ['id' => $book->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bookService->delete($book);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('book_index');
        }

        return $this->render(
            'book/delete.html.twig',
            [
                'form' => $form->createView(),
                'book' => $book,
            ]
        );
    }
}// end class
