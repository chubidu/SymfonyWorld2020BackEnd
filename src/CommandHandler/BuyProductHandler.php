<?php

declare(strict_types=1);

namespace App\CommandHandler;

use App\Command\BuyProduct;
use App\Entity\Channel\Channel;
use App\Entity\Order\Order;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class BuyProductHandler implements MessageHandlerInterface
{
    /**
     * @var FactoryInterface
     */
    private $orderFactory;
    /**
     * @var ChannelContextInterface
     */
    private $channelContext;
    /**
     * @var ProductVariantRepositoryInterface
     */
    private $productVariantRepository;
    /**
     * @var CartItemFactoryInterface
     */
    private $cartItemFactory;
    /**
     * @var OrderItemQuantityModifierInterface
     */
    private $orderItemQuantityModifier;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var OrderProcessorInterface
     */
    private $orderProcessor;

    public function __construct(
        FactoryInterface $orderFactory,
        ChannelContextInterface $channelContext,
        ProductVariantRepositoryInterface $productVariantRepository,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        EntityManagerInterface $entityManager,
        OrderProcessorInterface $orderProcessor
    ) {
        $this->orderFactory = $orderFactory;
        $this->channelContext = $channelContext;
        $this->productVariantRepository = $productVariantRepository;
        $this->cartItemFactory = $cartItemFactory;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->entityManager = $entityManager;
        $this->orderProcessor = $orderProcessor;
    }

    public function __invoke(BuyProduct $buyProduct): Order
    {
        /** @var Channel $channel */
        $channel = $this->channelContext->getChannel();

        // Create a new cart
        /** @var Order $order */
        $order = $this->orderFactory->createNew();
        $order->setChannel($channel);
        $order->setCurrencyCode($channel->getBaseCurrency()->getCode());
        $order->setLocaleCode($channel->getDefaultLocale()->getCode());
        $order->setTokenValue('SYMFONYWORLD2021');

        // Add product
        $productVariant = $this->productVariantRepository->findOneByCodeAndProductCode(
            $buyProduct->getProductVariantCode(),
            $buyProduct->getProductCode()
        );

        $orderItem = $this->cartItemFactory->createForCart($order);
        $orderItem->setVariant($productVariant);

        $this->orderItemQuantityModifier->modify($orderItem, 1);

        $this->orderProcessor->process($order);

        // Finish checkout

        // Persist everything
        $this->entityManager->persist($order);

        return $order;
    }
}
