<?php
/**
 * Rating controller.
 */

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Rating;
use App\Form\Type\RatingType;
use App\Service\RatingServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class RatingController.
 */
#[Route('/rating')]
class RatingController extends AbstractController
{

    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator
     * @param RatingServiceInterface $ratingService
     */
    public function __construct(private readonly TranslatorInterface $translator, private readonly RatingServiceInterface $ratingService) {
    }

    /**
     * Rate a book.
     *
     * @param Request $request
     * @param Book $book
     * @return Response
     */
    #[Route('/{id}/rate', name: 'book_rate', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    #[IsGranted('RATE', subject: 'book')]
    public function rate(Request $request, Book $book): Response
    {
        $rating = new Rating();
        $rating->setBook($book);
        $rating->setUser($this->getUser());

        $form = $this->createForm(
            RatingType::class,
            $rating,
            [
                'method' => 'POST',
                'action' => $this->generateUrl('book_rate', ['id' => $book->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ratingService->save($rating);

            $this->addFlash(
                'success',
                $this->translator->trans('message.rated_successfully')
            );

            return $this->redirectToRoute('book_show', ['id' => $book->getId()]);
        }

        return $this->render(
            'rating/rate.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Edit the rating.
     *
     * @param Request $request
     * @param Rating $rating
     * @return Response
     */
    #[Route('/{id}/edit', name: 'rating_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('EDIT', subject: 'rating')]
    public function edit(Request $request, Rating $rating): Response
    {
        $form = $this->createForm(
            RatingType::class,
            $rating,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('rating_edit', ['id' => $rating->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ratingService->save($rating);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('book_show', ['id' => $rating->getBook()->getId()]);
        }

        return $this->render(
            'rating/edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Delete the rating.
     *
     * @param Request $request
     * @param Rating $rating
     * @return Response
     */
    #[Route('/{id}/delete', name: 'rating_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[IsGranted('DELETE', subject: 'rating')]
    public function delete(Request $request, Rating $rating): Response
    {
        $bookId = $rating->getBook()->getId();

        $form = $this->createForm(
            FormType::class,
            $rating,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('rating_delete', ['id' => $rating->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ratingService->delete($rating);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('book_show', ['id' => $bookId]);
        }

        return $this->render(
            'rating/delete.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
