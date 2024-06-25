<?php
/**
 * Book list filters DTO.
 */

namespace App\Dto;

use App\Entity\Category;
use App\Entity\Tag;

/**
 * Class TaskListFiltersDto.
 */
class BookListFiltersDto
{

    /**
     *  Constructor.
     *
     * @param Category|null $category
     * @param Tag|null      $tag
     * @param string|null   $title
     * @param string|null   $author
     */
    public function __construct(public readonly ?Category $category, public readonly ?Tag $tag, public readonly ?string $title, public readonly ?string $author)
    {
    }
}
