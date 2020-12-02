<?php

declare(strict_types=1);

namespace App\Command;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Symfony\Component\Serializer\Annotation\Groups;

final class AddItemsFromWishlist implements OrderTokenValueAwareInterface
{
    /** @var string|null */
    private $orderTokenValue;
    /**
     * @var int
     * @Groups("cart:add_items")
     */
    private $wishlistId;

    public function __construct(int $wishlistId)
    {
        $this->wishlistId = $wishlistId;
    }

    public function getWishlistId(): int
    {
        return $this->wishlistId;
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }

    public function setOrderTokenValue(?string $orderTokenValue): void
    {
        $this->orderTokenValue = $orderTokenValue;
    }
}
