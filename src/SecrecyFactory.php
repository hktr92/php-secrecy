<?php

namespace Hktr92\Secrecy;

use Prewk\Result;
use RuntimeException;
use Throwable;

use function fopen;
use function urlencode;

/**
 * Class SecrecyFactory
 *
 * This Factory class helps you to easily create a Secrecy instance.
 */
final class SecrecyFactory
{
    /**
     * @param string $value The sensitive data to be guarded.
     *
     * @return Result<Secrecy, Throwable>
     */
    public static function create(string $value): Result
    {
        $value = urlencode($value);
        $resource = fopen("data:text/plain,$value", 'rb');

        if (false === $resource) {
            return new Result\Err(new RuntimeException('Failed to wrap secret value into a resource.'));
        }

        return new Result\Ok(new Secrecy($resource));
    }
}
