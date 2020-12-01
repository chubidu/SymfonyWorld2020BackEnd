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
    /** @var Generator */
    private $faker;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(Generator $faker, EntityManagerInterface $entityManager)
    {
        $this->faker = $faker;
        $this->entityManager = $entityManager;
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
