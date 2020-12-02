<?php

declare(strict_types=1);

namespace App\CommandHandler;

use App\Command\BuyProduct;
use App\Entity\Channel\Channel;
use App\Entity\Order\Order;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
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
     * @var ObjectManager
     */
    private $orderManager;
    /**
     * @var OrderProcessorInterface
     */
    private $orderProcessor;
    /**
     * @var StateMachineFactoryInterface
     */
    private $stateMachineFactory;
    /**
     * @var CustomerContextInterface
     */
    private $customerContext;

    public function __construct(
        FactoryInterface $orderFactory,
        ChannelContextInterface $channelContext,
        ProductVariantRepositoryInterface $productVariantRepository,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        EntityManagerInterface $manager,
        OrderProcessorInterface $orderProcessor,
        StateMachineFactoryInterface $stateMachineFactory,
        CustomerContextInterface $customerContext
    ) {
        $this->orderFactory = $orderFactory;
        $this->channelContext = $channelContext;
        $this->productVariantRepository = $productVariantRepository;
        $this->cartItemFactory = $cartItemFactory;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->orderManager = $manager;
        $this->orderProcessor = $orderProcessor;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->customerContext = $customerContext;
    }

    public function __invoke(BuyProduct $buyProduct): Order
    {
        /** @var Channel $channel */
        $channel = $this->channelContext->getChannel();

        // Create a cart
        /** @var Order $cart */
        $cart = $this->orderFactory->createNew();
        $cart->setChannel($channel);
        $cart->setCurrencyCode($channel->getBaseCurrency()->getCode());
        $cart->setLocaleCode($channel->getDefaultLocale()->getCode());
        // $cart->setTokenValue('SymfonyWorld2020');

        // Add product to cart
        $productVariant = $this->productVariantRepository->findOneByCodeAndProductCode(
            $buyProduct->getProductVariantCode(),
            $buyProduct->getProductCode()
        );

        $cartItem = $this->cartItemFactory->createForCart($cart);
        $cartItem->setVariant($productVariant);

        $this->orderItemQuantityModifier->modify($cartItem, 1);

        // Process an order
        // $this->orderProcessor->process($cart);

        // Assign customer to cart
        $customer = $this->customerContext->getCustomer();

        if (null === $customer) {
            throw new \InvalidArgumentException();
        }

        $cart->setCustomer($customer);

        // Finish the checkout process
        $stateMachine = $this->stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH);

        $stateMachine->apply('buy');

        // Persist new cart to database
        $this->orderManager->persist($cart);

        // Return this car
        return $cart;
    }
}
