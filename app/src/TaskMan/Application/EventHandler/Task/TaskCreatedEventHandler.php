<?php

declare(strict_types=1);

namespace App\TaskMan\Application\EventHandler\Task;

use App\TaskMan\Domain\Model\Task\TaskCreatedEvent;
use Psr\Log\LoggerInterface;

final class TaskCreatedEventHandler
{

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(TaskCreatedEvent $event): void
    {
        $this->logger->info(
          sprintf(
            'A new Task was created with ID: %s',
            $event->getTask()->getId()
          )
        );
    }

}