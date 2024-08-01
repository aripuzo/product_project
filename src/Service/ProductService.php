<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use Symfony\Component\Serializer\SerializerInterface;

class ProductService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createProduct(string $name, int $price): bool
    {
        $product = new Product();
        $product->setName($name);
        $product->setPrice($price);

        // Save the user to the database
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return true;
    }

    public function createProductSerialized(string $data, SerializerInterface $serializer): bool
    {
        $product = $serializer->deserialize($data, Product::class, 'json');
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return true;
    }
}
