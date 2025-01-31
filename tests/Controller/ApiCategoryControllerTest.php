<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiCategoryControllerTest extends WebTestCase
{
    public function testCreateCategory(): void
    {
        $client = static::createClient();
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');

        $crawler = $client->request(
            method: 'POST',
            uri: '/api/login_check',
            server: [
                'CONTENT_TYPE' => 'application/json'
            ],
            content: json_encode([
                'username' => 'lougarre2t',
                'password' => 'Azerty01'
            ], JSON_THROW_ON_ERROR)
        );

        $authResponse = json_decode($client->getResponse()->getContent(), true);
        $token = $authResponse['token'];

        $crawler = $client->request(
            method: 'POST',
            uri: '/categories/',
            server: [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ],
            content: json_encode([
                'name' => 'Un nom',
                'description' => 'Une description'
            ], JSON_THROW_ON_ERROR)
        );

        $response = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseIsSuccessful();
        self::assertArrayHasKey('name', $response);
        self::assertArrayHasKey('description', $response);
        self::assertEquals('Un nom', $response['name']);
        self::assertEquals('Une description', $response['description']);

        $createdCategory = $entityManager->getRepository(Category::class)->find($response['id']);

        self::assertEquals('Un nom', $createdCategory->getName());
        self::assertEquals('Une description', $createdCategory->getDescription());
    }

    public function tearDown(): void
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $category = $entityManager->getRepository(Category::class)->findOneBy(['name' => 'Un nom']);
        $entityManager->remove($category);
        $entityManager->flush();
    }
}
