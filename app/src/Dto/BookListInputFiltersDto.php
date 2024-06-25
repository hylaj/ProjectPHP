<?php
/**
 * Book list input filters DTO.
 */

namespace App\Dto;

/**
 * Class BookListInputFiltersDto.
 */
class BookListInputFiltersDto
{
    /**
     * Constructor.
     *
     * @param int|null    $categoryId   Category ID filter
     * @param int|null    $tagId        Tag ID filter
     * @param string|null $titleSearch  Title search filter
     * @param string|null $authorSearch Author search filter
     */
    public function __construct(public readonly ?int $categoryId = null, public readonly ?int $tagId = null, public readonly ?string $titleSearch = null, public readonly ?string $authorSearch = null)
    {
    }
}
