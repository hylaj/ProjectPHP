<?php
/**
 * BookListInputFiltersDto resolver.
 */

namespace App\Resolver;

use App\Dto\BookListInputFiltersDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * BookListInputFiltersDtoResolver class.
 */
class BookListInputFiltersDtoResolver implements ValueResolverInterface
{
    /**
     * Returns the possible value(s).
     *
     * @param Request          $request  HTTP Request
     * @param ArgumentMetadata $argument Argument metadata
     *
     * @return iterable Iterable
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();

        if (!$argumentType || !is_a($argumentType, BookListInputFiltersDto::class, true)) {
            return [];
        }

        $categoryId = $request->query->get('categoryId');
        $tagId = $request->query->get('tagId');
        $titleSearch = $request->query->get('titleSearch') ?? null;
        $authorSearch = $request->query->get('authorSearch') ?? null;

        return [new BookListInputFiltersDto($categoryId, $tagId, $titleSearch, $authorSearch)];
    }
}
