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
     * @param int|null $categoryId
     * @param int|null $tagId
     * @param string|null $titleSearch
     * @param string|null $authorSearch
     */
    public function __construct(public readonly ?int $categoryId = null, public readonly ?int $tagId = null, public readonly ?string $titleSearch = null, public readonly ?string $authorSearch = null) {
    }
}
