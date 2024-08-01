<?php

namespace App\Entity;

class ProductFactory
{
    public static function create(string $name, float $price): Product
    {
        $product = new Product();
        $product->setName($name);
        $product->setPrice($price);
        return $product;
    }
}
