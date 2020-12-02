<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Customer\Customer;
use App\Entity\Product\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="app_wishlist")
 * @ApiResource(
 *     routePrefix="/shop/account",
 *     denormalizationContext={"groups"={"shop:wishlist:create", "shop:wishlist:update"}}
 * )
 */
class Wishlist
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Groups({"shop:wishlist:create", "shop:wishlist:update"})
     */
    private $title;

    /**
     * @var Product[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\Product\Product")
     * @ORM\JoinTable(
     *     name="wishlists_products",
     *     joinColumns={@ORM\JoinColumn(name="wishlist_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")})
     * @Groups({"shop:wishlist:create", "shop:wishlist:update"})
     */
    private $products;

    /**
     * @var Customer|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private $customer;

    public function __construct(string $title)
    {
        $this->products = new ArrayCollection();
        $this->title = $title;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function setProducts(Collection $products): void
    {
        $this->products = $products;
    }

    public function addProduct(Product $product): void
    {
        $this->products->add($product);
    }

    public function removeProduct(Product $product): void
    {
        $this->products->remove($product);
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): void
    {
        $this->customer = $customer;
    }
}
