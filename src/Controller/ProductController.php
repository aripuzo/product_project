<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api')]
class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository, SerializerInterface $serializer): Response
    {
        $products = $productRepository->findAll();
        $data = $serializer->serialize($products, 'json');

        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/products', name: 'app_product_new', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ): Response {
        //$request->validate();
        $requestData = $request->getContent();

        $product = $serializer->deserialize($requestData, Product::class, 'json');

        if (!$product->getName() || !$product->getPrice()) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        $entityManager->persist($product);
        $entityManager->flush();

        $data = $serializer->serialize($product, 'json');

        return new JsonResponse(['message' => 'Product created!', 'product' => json_decode($data)], 201);
    }

    #[Route('/products/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, SerializerInterface $serializer, int $id): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            return $this->json('No project found for id ' . $id, 404);
        }
        $data = $serializer->serialize($product, 'json');
        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/products/{id}', name: 'app_product_edit', methods: ['PUT'])]
    public function update(
        Product $product,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ): Response {
        $requestData = $request->getContent();
        if (!$requestData) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }
        $updatedProduct = $serializer->deserialize($requestData, Product::class, 'json');

        $product->setName($updatedProduct->getName());
        $product->setPrice($updatedProduct->getPrice());

        $entityManager->flush();

        return new Response('Product updated!', 200);
    }

    #[Route('/products/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    public function delete(Product $product, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($product);
        $entityManager->flush();

        return new Response('Product deleted!', 200);
    }
}
