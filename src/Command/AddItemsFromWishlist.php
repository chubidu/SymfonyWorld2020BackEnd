<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Serializer\Annotation\Groups;

final class AddItemsFromWishlist
{
    /**
     * @var int
     * @Groups("cart:add_item")
     */
    public $wishlistId;

    public function __construct(int $wishlistId)
    {
        $this->wishlistId = $wishlistId;
    }
}
