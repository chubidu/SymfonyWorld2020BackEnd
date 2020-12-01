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
use Symfony\Component\VarDumper\VarDumper;

final class WishlistFixture extends AbstractFixture
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var Generator */
    private $faker;

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        CustomerRepositoryInterface $customerRepository,
        EntityManagerInterface $entityManager,
        Generator $faker
    ) {
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->entityManager = $entityManager;
        $this->faker = $faker;

        $this->optionsResolver = new OptionsResolver();
        $this->configureOptions($this->optionsResolver);
    }

    public function load(array $options): void
    {
        for ($i = 0; $i < $options['count']; $i++) {
            $resolvedOptions = $this->optionsResolver->resolve();

            $wishlist = new Wishlist();
            $wishlist->setTitle($this->faker->word);
            $wishlist->setCustomer($resolvedOptions['customer']);
            $wishlist->setProducts($resolvedOptions['products']);

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
            ->setAllowedTypes('customer', ['null', 'string', Customer::class])

            ->setDefault('products', LazyOption::randomOnes($this->productRepository, 3))
            ->setAllowedTypes('products', ['null', 'array'])
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
