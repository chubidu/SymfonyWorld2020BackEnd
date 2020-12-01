<?php

declare(strict_types=1);

namespace App\CommandHandler;

use App\Command\AddItemsFromWishlist;
use App\Entity\Order\Order;
use App\Entity\Product\Product;
use App\Entity\Wishlist;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AddItemsFromWishlistHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var CartContextInterface */
    private $cartContext;

    /** @var CartItemFactoryInterface */
    private $cartItemFactory;

    /** @var OrderItemQuantityModifierInterface */
    private $orderItemQuantityModifier;

    /** @var OrderModifierInterface */
    private $orderModifier;

    public function __construct(
        EntityManagerInterface $entityManager,
        CartContextInterface $cartContext,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderModifierInterface $orderModifier
    ) {
        $this->entityManager = $entityManager;
        $this->cartContext = $cartContext;
        $this->cartItemFactory = $cartItemFactory;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->orderModifier = $orderModifier;
    }

    public function __invoke(AddItemsFromWishlist $command): Order
    {
        $wishlistRepository = $this->entityManager->getRepository(Wishlist::class);
        /** @var Wishlist $wishlist */
        $wishlist = $wishlistRepository->find($command->wishlistId);
        /** @var Order $cart */
        $cart = $this->cartContext->getCart();

        /** @var Product $product */
        foreach ($wishlist->getProducts() as $product) {
            $this->addItemWithProduct($cart, $product);
        }

        return $cart;
    }

    private function addItemWithProduct(Order $cart, Product $product): void
    {
        $cartItem = $this->cartItemFactory->createForProduct($product);
        $this->orderItemQuantityModifier->modify($cartItem, 1);

        $this->orderModifier->addToOrder($cart, $cartItem);
    }
}
