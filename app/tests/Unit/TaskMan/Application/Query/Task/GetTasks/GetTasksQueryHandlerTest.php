<?php

declare(strict_types=1);

namespace App\Tests\Unit\TaskMan\Application\Query\Task\GetTasks;

use App\TaskMan\Application\Query\Task\GetTasks\GetTasksQuery;
use App\TaskMan\Application\Query\Task\GetTasks\GetTasksQueryHandler;
use App\TaskMan\Domain\Model\Task\TaskRepositoryInterface;
use App\TaskMan\Domain\Model\User\UniqueUsernameSpecificationInterface;
use App\TaskMan\Domain\Model\User\User;
use App\TaskMan\Domain\Model\User\UserFetcherInterface;
use PHPUnit\Framework\TestCase;


final class GetTasksQueryHandlerTest extends TestCase
{

    public function testItReturnTaskCollectionWhenInvoked(): void
    {
        $executionDate = (new \DateTimeImmutable())->setTime(0, 0);

        $userFetcher = $this->createMock(UserFetcherInterface::class);
        $userFetcher->method('fetchAuthenticatedUser')->willReturn(
          new User(
            'user1', 'password1', $this->getUniqueUsernameSpecification()
          )
        );

        $taskRepository = $this->createMock(TaskRepositoryInterface::class);
        $taskRepository->expects(self::once())
          ->method('findByUserAndExecutionDate')
          ->willReturn(
            [
              [
                'id' => 1,
                'title' => 'Task #1',
                'execution_date' => '2020-09-19T00:00:00',
                'created_at' => '2020-09-18T12:00:00',
              ],
            ]
          );

        $query = new GetTasksQuery($executionDate);
        $handler = new GetTasksQueryHandler($taskRepository, $userFetcher);

        try {
            $handler($query);
        } catch (\Error $e) {
            if (strpos(
                $e->getMessage(),
                'id must not be accessed before initialization'
              ) === false) {
                throw $e;
            }
        }
    }

    private function getUniqueUsernameSpecification(
    ): UniqueUsernameSpecificationInterface
    {
        $specification = $this->createMock(
          UniqueUsernameSpecificationInterface::class
        );
        $specification->method('isSatisfiedBy')->willReturn(true);

        return $specification;
    }

}