<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use App\Shared\Domain\Exception\InvalidInputDataException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class ApiExceptionSubscriber implements EventSubscriberInterface
{

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
          KernelEvents::EXCEPTION => 'onException',
        ];
    }

    public function onException(ExceptionEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (strpos($request->getPathInfo(), '/api/') !== 0) {
            return;
        }

        $throwable = $event->getThrowable();

        if ($throwable instanceof HandlerFailedException && \count(
            $throwable->getNestedExceptions()
          ) > 0) {
            $throwable = $throwable->getNestedExceptions()[0];
        }

        switch (true) {
            case $throwable instanceof InvalidInputDataException:
                $event->setResponse(
                  new JsonResponse(
                    ['error' => $event->getThrowable()->getMessage()],
                    Response::HTTP_BAD_REQUEST
                  )
                );
                break;
            default:
                $event->setResponse(
                  new JsonResponse(
                    $event->getThrowable()->getMessage(),
                    Response::HTTP_INTERNAL_SERVER_ERROR
                  )
                );
                break;
        }
    }

}