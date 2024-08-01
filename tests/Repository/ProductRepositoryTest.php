<?php

namespace App\Tests\Repository;

use App\Entity\ProductFactory;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductRepositoryTest extends KernelTestCase
{

    private EntityManagerInterface $entityManager;

    private ProductRepository $productRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $container = static::getContainer();

        $this->productRepository = $container->get(ProductRepository::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }


    public function testCreatePost(): void
    {
        $entity = ProductFactory::create("test product", 10);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        $this->assertNotNull($entity->getId());

        $byId = $this->productRepository->findOneBy(["id" => $entity->getId()]);
        $this->assertEquals("test product", $byId->getName());
        $this->assertEquals(10, $byId->getPrice());
    }
}
