<?php

declare(strict_types=1);

namespace App\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Wishlist;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Customer\Context\CustomerContextInterface;

final class WishlistQueryExtension implements QueryCollectionExtensionInterface
{
    /**
     * @var CustomerContextInterface
     */
    private $customerContext;

    public function __construct(CustomerContextInterface $customerContext)
    {
        $this->customerContext = $customerContext;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ) {
        if ($resourceClass !== Wishlist::class) {
            return;
        }

        $customer = $this->customerContext->getCustomer();

        if (null === $customer) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere(sprintf('%s.customer = :customer', $rootAlias))
            ->setParameter('customer', $customer)
        ;
    }
}
