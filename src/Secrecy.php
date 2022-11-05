<?php

namespace Hktr92\Secrecy;

use InvalidArgumentException;
use Prewk\Result;
use RuntimeException;
use Throwable;

use function is_resource;
use function stream_get_contents;

/**
 * Class Secrecy
 *
 * Secrecy holds your sensitive data as safe as possible from leaking.
 */
final class Secrecy
{
    /** @var resource */
    private $value;

    /**
     * Constructs a secret value.
     *
     * It requires a valid resource, so that `var_export()` won't leak the inner secret.
     *
     * @param resource $value A valid resource, e.g. `fopen()`, that holds the secret value.
     */
    public function __construct($value)
    {
        // Panic if no valid resource is given.
        if (false === is_resource($value)) {
            throw new InvalidArgumentException("Argument #1 of " . __CLASS__ . " needs to be a valid resource.");
        }

        $this->value = $value;
    }

    /**
     * Exposes the inner secret value from data stream.
     *
     * @return Result<string, Throwable>
     */
    public function expose(): Result
    {
        $result = stream_get_contents($this->value);

        if (false === $result) {
            return new Result\Err(new RuntimeException('Unable to read secret value.'));
        }

        return new Result\Ok($result);
    }

    /**
     * Disables cloning of the object.
     */
    private function __clone()
    {
    }

    /**
     * Prevents leaking in accidental toString conversion.
     */
    public function __toString(): string
    {
        return "<secret>";
    }

    /**
     * Prevents leaking in \serialize()
     */
    public function __serialize(): array
    {
        return ['value' => null];
    }

    /**
     * Prevents injection of undesired data from \unserialize()
     */
    public function __unserialize(array $data): void
    {
        $this->value = null;
    }

    /**
     * Prevents leaking into var_dump()
     */
    public function __debugInfo(): array
    {
        return ['value' => (string)$this];
    }

    /**
     * Cleaning up the resource, if needed.
     */
    public function __destruct()
    {
        if (is_resource($this->value)) {
            fclose($this->value);
        }
    }
}
