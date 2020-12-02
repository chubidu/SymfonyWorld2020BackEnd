<?php

declare(strict_types=1);

namespace App\Fixture;

use App\Entity\Customer\Customer;
use App\Entity\Wishlist;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Generator;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class WishlistFixture extends AbstractFixture
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var Generator */
    private $faker;

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        ProductRepositoryInterface $productRepository,
        EntityManagerInterface $entityManager,
        Generator $faker
    ) {
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->entityManager = $entityManager;
        $this->faker = $faker;

        $this->optionsResolver = new OptionsResolver();
        $this->configureOptions($this->optionsResolver);
    }

    public function load(array $options): void
    {
        for ($i = 0; $i < $options['count']; $i++) {
            $resolvedOptions = $this->optionsResolver->resolve();

            $wishlist = new Wishlist($this->faker->word);
            $wishlist->setCustomer($resolvedOptions['customer']);
            $wishlist->setProducts(new ArrayCollection($resolvedOptions['products']));

            $this->entityManager->persist($wishlist);
        }

        $this->entityManager->flush();
    }

    public function getName(): string
    {
        return 'wishlist';
    }

    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver
            ->setDefault('customer', LazyOption::randomOne($this->customerRepository))
            ->setAllowedTypes('customer', ['null', Customer::class])

            ->setDefault('products', LazyOption::randomOnes($this->productRepository, 3))
            ->setAllowedTypes('products', 'array')
        ;
    }

    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
            ->integerNode('count')
        ;
    }
}
