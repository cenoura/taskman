<?php

declare(strict_types=1);

namespace App\TaskMan\UI\Rest\Authentication;

use App\TaskMan\Application\Command\Authentication\CreateAuthToken\CreateAuthTokenCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\Assert\Assert;

final class CreateAuthTokenAction
{

    use HandleTrait;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
    }

    /**
     * @Route("/api/auth-token", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $userData = $request->request->all();

        Assert::minLength($userData['username'], 1);
        Assert::minLength($userData['password'], 6);

        $token = $this->handle(
          new CreateAuthTokenCommand(
            $userData['username'],
            $userData['password']
          )
        );

        return new JsonResponse(['token' => $token], Response::HTTP_OK);
    }

}