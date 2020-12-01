<?php

declare(strict_types=1);

namespace App\Command;

class BuyProduct
{
    /** @var string */
    private $productCode;

    /** @var string */
    private $productVariantCode;

    public function __construct(string $productCode, string $productVariantCode)
    {
        $this->productCode = $productCode;
        $this->productVariantCode = $productVariantCode;
    }

    public function getProductCode(): string
    {
        return $this->productCode;
    }

    public function getProductVariantCode(): string
    {
        return $this->productVariantCode;
    }
}
