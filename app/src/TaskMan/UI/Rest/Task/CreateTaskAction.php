<?php

declare(strict_types=1);

namespace App\TaskMan\UI\Rest\Task;

use App\Shared\Domain\Service\Assert;
use App\TaskMan\Application\Command\Task\CreateTask\CreateTaskCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

final class CreateTaskAction
{

    use HandleTrait;

    private const DATE_FORMAT = 'Y-m-d';

    private RouterInterface $router;

    public function __construct(
      MessageBusInterface $commandBus,
      RouterInterface $router
    ) {
        $this->messageBus = $commandBus;
        $this->router = $router;
    }

    /**
     * @Route("/api/tasks", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $taskData = $request->request->all();

        Assert::dateFromString($taskData['execution_date'], self::DATE_FORMAT);

        $executionDate = \DateTimeImmutable::createFromFormat(
          self::DATE_FORMAT,
          $taskData['execution_date']
        );

        $command = new CreateTaskCommand(
          $taskData['title'],
          $executionDate
        );

        $this->handle($command);

        return new JsonResponse(
          null,
          Response::HTTP_CREATED
        );
    }

}