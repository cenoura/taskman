<?php

declare(strict_types=1);

namespace App\Tests\Unit\TaskMan\Application\Query\Task\DTO;

use App\TaskMan\Application\Query\Task\DTO\TaskDTO;
use PHPUnit\Framework\TestCase;

class TaskDTOTest extends TestCase
{

    public function testItThrowsExceptionWhenInvalidDataArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Error creating TaskDTO/');

        $taskData = [
          'title' => 'Task 1',
        ];

        TaskDTO::fromArray($taskData);
    }

    public function testItPassesWhenValidDataArray(): void
    {
        $taskData = [
          'id' => 1,
          'title' => 'Task 1',
          'execution_date' => '2020-09-15 12:00:00',
          'created_at' => '2020-09-14 08:30:27',
        ];

        $taskDTO = TaskDTO::fromArray($taskData);

        self::assertInstanceOf(TaskDTO::class, $taskDTO);
    }

}
