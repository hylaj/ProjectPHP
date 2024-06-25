<?php
/**
 * Book list filters DTO.
 */

namespace App\Dto;

use App\Entity\Category;
use App\Entity\Tag;

/**
 * Class BookListFiltersDto.
 */
class BookListFiltersDto
{
    /**
     * Constructor.
     *
     * @param Category|null $category Category entity
     * @param Tag|null      $tag      Tag entity
     * @param string|null   $title    Book title
     * @param string|null   $author   Book author
     */
    public function __construct(public readonly ?Category $category, public readonly ?Tag $tag, public readonly ?string $title, public readonly ?string $author)
    {
    }
}
