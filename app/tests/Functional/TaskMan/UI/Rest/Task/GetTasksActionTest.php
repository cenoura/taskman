<?php

declare(strict_types=1);

namespace App\Tests\Functional\TaskMan\UI\Rest\Task;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GetTasksActionTest extends WebTestCase
{

    private function createAuthenticatedClient(
      $username = 'user1',
      $password = 'password1'
    ) {
        $client = static::createClient();
        $client->request(
          'POST',
          '/api/auth-token',
          [],
          [],
          ['CONTENT_TYPE' => 'application/json'],
          json_encode(
            [
              'username' => $username,
              'password' => $password,
            ]
          )
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        self::ensureKernelShutdown();

        $client = static::createClient();
        $client->setServerParameter(
          'HTTP_Authorization',
          sprintf('Bearer %s', $data['token'])
        );

        return $client;
    }

    public function testGetUserTasksWhenUserLoggedIn(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/api/tasks');
        $this->assertResponseIsSuccessful();
    }

    public function testGetErrorWhenUserNotLoggedIn(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/tasks');
        $this->assertResponseStatusCodeSame(401);
    }

}