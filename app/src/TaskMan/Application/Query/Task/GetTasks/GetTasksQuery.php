<?php

declare(strict_types=1);

namespace App\TaskMan\Application\Query\Task\GetTasks;

final class GetTasksQuery
{

    private \DateTimeImmutable $executionDate;

    public function __construct(\DateTimeImmutable $executionDate)
    {
        $this->executionDate = $executionDate;
    }

    public function getExecutionDate(): \DateTimeImmutable
    {
        return $this->executionDate;
    }

}