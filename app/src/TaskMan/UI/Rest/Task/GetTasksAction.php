<?php

declare(strict_types=1);

namespace App\TaskMan\UI\Rest\Task;

use App\Shared\Domain\Service\Assert;
use App\Shared\Infrastructure\ValueObject\CollectionData;
use App\TaskMan\Application\Query\Task\GetTasks\GetTasksQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class GetTasksAction
{

    use HandleTrait;

    private const DATE_FORMAT = 'Y-m-d';

    private NormalizerInterface $normalizer;

    public function __construct(
      MessageBusInterface $queryBus,
      NormalizerInterface $normalizer
    ) {
        $this->messageBus = $queryBus;
        $this->normalizer = $normalizer;
    }

    /**
     * @Route("/api/tasks", methods={"GET"})
     *
     * @param Request $request
     *
     * @return Response
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function __invoke(Request $request): Response
    {
        $executionDate = $request->query->get('execution_date');

        if ($executionDate === null) {
            $executionDate = new \DateTimeImmutable();
        } else {
            Assert::dateFromString($executionDate, self::DATE_FORMAT);
            $executionDate = \DateTimeImmutable::createFromFormat(
              self::DATE_FORMAT,
              $executionDate
            );
        }

        $query = new GetTasksQuery(
          $executionDate
        );

        /** @var CollectionData $tasksCollection */
        $tasksCollection = $this->handle($query);

        return new JsonResponse(
          $this->normalizer->normalize(
            $tasksCollection->getData(),
            '',
            ['groups' => 'task_view']
          ),
          Response::HTTP_OK
        );
    }

}