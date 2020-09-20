<?php

declare(strict_types=1);

namespace App\Tests\Functional\TaskMan\Infrastructure\Repository;

use App\TaskMan\Domain\Model\Task\Task;
use App\TaskMan\Domain\Model\Task\TaskRepositoryInterface;
use App\TaskMan\Domain\Model\User\User;
use App\TaskMan\Domain\Model\User\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{

    private $em;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()
          ->get('doctrine')
          ->getManager();
    }

    public function testFindByUserAndExecutionDate()
    {
        $executionDate = (new \DateTimeImmutable())->setTime(0, 0);

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $this->em->getRepository(User::class);
        $user1 = $userRepository->findOneBy(['username' => 'user1']);
        $user2 = $userRepository->findOneBy(['username' => 'user2']);

        /** @var TaskRepositoryInterface $taskRepository */
        $taskRepository = $this->em
          ->getRepository(Task::class);

        $tasksUser1 = $taskRepository
          ->findBy(
            ['user' => $user1->getId(), 'executionDate' => $executionDate]
          );

        $tasksUser2 = $taskRepository
          ->findBy(
            ['user' => $user2->getId(), 'executionDate' => $executionDate]
          );

        self::assertCount(3, $tasksUser1);
        self::assertCount(2, $tasksUser2);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null;
    }

}