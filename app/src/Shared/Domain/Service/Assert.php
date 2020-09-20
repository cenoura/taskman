<?php

declare(strict_types=1);

namespace App\Shared\Domain\Service;

use App\Shared\Domain\Exception\InvalidInputDataException;

final class Assert
{

    public static function dateFromString(
      string $value,
      string $format,
      string $message = ''
    ): void {
        $date = \DateTimeImmutable::createFromFormat($format, $value);

        if ($date === false) {
            static::reportInvalidArgument(
              sprintf(
                $message === '' ? 'Date string "%s" should be like "%s"' : $message,
                $value,
                $format
              )
            );
        }
    }

    /**
     * @param string $message
     *
     * @throws InvalidInputDataException
     */
    protected static function reportInvalidArgument(string $message): void
    {
        throw new InvalidInputDataException($message);
    }

}