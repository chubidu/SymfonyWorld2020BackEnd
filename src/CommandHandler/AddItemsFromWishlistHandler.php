<?php

declare(strict_types=1);

namespace App\CommandHandler;

use App\Command\AddItemsFromWishlist;
use App\Entity\Order\Order;
use App\Entity\Order\OrderItem;
use App\Entity\Product\Product;
use App\Entity\Wishlist;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AddItemsFromWishlistHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var ObjectRepository */
    private $wishlistRepostiory;
    /** @var OrderRepositoryInterface */
    private $orderRepository;
    /** @var CartItemFactoryInterface */
    private $cartItemFactory;
    /** @var OrderItemQuantityModifierInterface */
    private $orderItemQuantityModifier;
    /**
     * @var OrderProcessorInterface
     */
    private $orderProcessor;

    public function __construct(
        EntityManagerInterface $entityManager,
        OrderRepositoryInterface $orderRepository,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderProcessorInterface $orderProcessor
    ) {
        $this->entityManager = $entityManager;
        $this->wishlistRepostiory = $entityManager->getRepository(Wishlist::class);

        $this->orderRepository = $orderRepository;
        $this->cartItemFactory = $cartItemFactory;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->orderProcessor = $orderProcessor;
    }

    public function __invoke(AddItemsFromWishlist $addItemsFromWishlist): Order
    {
        /** @var Wishlist $wishlist */
        $wishlist = $this->wishlistRepostiory->find($addItemsFromWishlist->getWishlistId());

        /** @var Order $cart */
        $cart = $this->orderRepository->findOneBy(['tokenValue' => $addItemsFromWishlist->getOrderTokenValue()]);

        foreach ($wishlist->getProducts() as $product) {
            $cartItem = $this->getItem($product, $cart);

            $this->orderItemQuantityModifier->modify($cartItem, $cartItem->getQuantity() + 1);
        }

        $this->orderProcessor->process($cart);

        return $cart;
    }

    private function getItem(Product $product, Order $cart): OrderItemInterface
    {
        /** @var OrderItem $item */
        foreach ($cart->getItems() as $item) {
            if ($item->getProduct() === $product) {
                return $item;
            }
        }

        $cartItem = $this->cartItemFactory->createForProduct($product);
        $cartItem->setOrder($cart);

        return $cartItem;
    }
}
