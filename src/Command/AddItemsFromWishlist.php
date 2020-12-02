<?php

declare(strict_types=1);

namespace App\Command;

final class AddItemsFromWishlist
{
    /** @var string */
    private $orderToken;
    /** @var int */
    private $wishlistId;

    public function __construct(string $orderToken, int $wishlistId)
    {
        $this->orderToken = $orderToken;
        $this->wishlistId = $wishlistId;
    }

    public function getOrderToken(): string
    {
        return $this->orderToken;
    }

    public function getWishlistId(): int
    {
        return $this->wishlistId;
    }
}
