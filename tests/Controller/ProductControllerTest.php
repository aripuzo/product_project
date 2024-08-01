<?php

namespace App\Test\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ProductControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/products');

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('Content-Type', 'application/json');
        self::assertJson($client->getResponse()->getContent());
    }

    public function testCreate(): void
    {
        $client = static::createClient();
        $payload = ['name' => "Test", "price" => 10];
        $client->request('POST', '/api/products', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload));

        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('Content-Type', 'application/json');
        self::assertJson($client->getResponse()->getContent());
    }

    public function testProductCrudFlow(): void
    {
        $client = static::createClient();

        // 1. create a new product
        $payload = ['name' => "Test", "price" => 10];
        $client->request('POST', '/api/products', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('product', $data);
        $data = $data['product'];
        $this->assertEquals($payload['name'], $data['name']);


        // 2. get the newly created product.
        $client->request('GET', '/api/products/'. $data['id']);

        $getByIdResponse = $client->getResponse();
        $getData = $this->getContainer()->get('serializer')->deserialize($getByIdResponse->getContent(), Product::class, "json");
        $this->assertEquals($payload['name'], $getData->getName());
        $this->assertEquals($payload['price'], $getData->getPrice());

        // 3. update the existing post.
        $updatePayload = ['name' => "New Product", "price" => 15];
        $client->request('PUT', '/api/products/'. $data['id'], [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($updatePayload));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        // 4. verify the updated post.
        $client->request('GET', '/api/products/'.$data['id']);

        $updatedResponse = $client->getResponse();
        $updatedData = $this->getContainer()->get('serializer')->deserialize($updatedResponse->getContent(), Product::class, "json");
        $this->assertEquals($updatePayload['name'], $updatedData->getName());
        $this->assertEquals($updatePayload['price'], $updatedData->getPrice());

        // 5. delete the post
        $client->request('DELETE', '/api/products/' . $data['id']);
        $client->getResponse();
        $this->assertResponseStatusCodeSame(200);

        // 9. verify the post is deleted.
        $client->request('GET', '/api/products/' . $data['id']);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testShowNotFound(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/products/1');

        self::assertResponseStatusCodeSame(404);
    }

    public function testUpdateNotFound(): void
    {
        $client = static::createClient();
        $payload = ['name' => "Update product", "price" => 10];
        $client->request('PUT', '/api/products/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload));

        self::assertResponseStatusCodeSame(404);
    }

    public function testDeleteNotFound(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/products/1');

        self::assertResponseStatusCodeSame(404);
    }
}