<?php
/**
 * Category Controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Book;
use App\Repository\CategoryRepository;
use App\Repository\BookRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

/**
 *
 */
#[Route('/category')]
class CategoryController extends AbstractController
{
    /**
     * @param CategoryRepository $categoryRepository
     * @param PaginatorInterface $paginator
     * @param int $page
     * @return Response
     */
    #[Route(name: 'category_index', methods: 'GET')]
    public function index(CategoryRepository $categoryRepository, PaginatorInterface $paginator, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $paginator->paginate(
            $categoryRepository->queryAll(),
            $page,
            CategoryRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render('category/index.html.twig', ['pagination' => $pagination]);
    }// end index()

    /**
     * @param Category $category
     * @param BookRepository $bookRepository
     * @return Response
     */
    #[Route(
        '/{id}',
        name: 'category_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(Category $category, BookRepository $bookRepository): Response
    {
        $books=$bookRepository->findTasksByCategory($category);
        return $this->render(
            'category/show.html.twig',
            ['category' => $category,
                'books' => $books]
        );
    }// end show()
}// end class