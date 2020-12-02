<?php

declare(strict_types=1);

namespace App\Fixture;

use App\Entity\Supplier;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Generator;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class SupplierFixture extends AbstractFixture
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var Generator */
    private $faker;

    public function __construct(EntityManagerInterface $entityManager, Generator $faker)
    {
        $this->entityManager = $entityManager;
        $this->faker = $faker;
    }

    public function load(array $options): void
    {
        for ($i = 0; $i < $options['count']; $i++) {
            $supplier = new Supplier();
            $supplier->setName($this->faker->company);

            $this->entityManager->persist($supplier);
        }

        $this->entityManager->flush();
    }

    public function getName(): string
    {
        return 'supplier';
    }

    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
            ->integerNode('count')
        ;
    }
}
