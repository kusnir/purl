<?php

declare(strict_types=1);

namespace Purl;

use ArrayAccess;

/**
 * AbstractPart class is implemented by each part of a Url where necessary.
 */
abstract class AbstractPart implements ArrayAccess
{
    protected bool $initialized = false;

    /** @var array<string, mixed> */
    protected array $data = [];

    /** @var string[] */
    protected array $partClassMap = [];

    public function getData(): array
    {
        $this->initialize();

        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->initialize();

        $this->data = $data;
    }

    public function isInitialized(): bool
    {
        return $this->initialized;
    }

    public function has(string $key): bool
    {
        $this->initialize();

        return isset($this->data[$key]);
    }

    public function get(string $key) : mixed
    {
        $this->initialize();

        return $this->data[$key] ?? null;
    }

    public function set(string $key, mixed $value): AbstractPart
    {
        $this->initialize();
        $this->data[$key] = $value;

        return $this;
    }

    public function add(mixed $value): AbstractPart
    {
        $this->initialize();
        $this->data[] = $value;

        return $this;
    }

    public function remove(string $key): AbstractPart
    {
        $this->initialize();

        unset($this->data[$key]);

        return $this;
    }

    public function __isset(string $key): bool
    {
        return $this->has($key);
    }

    public function __get(string $key) : mixed
    {
        return $this->get($key);
    }

    public function __set(string $key, mixed $value): void
    {
        $this->set($key, $value);
    }

    public function __unset(string $key): void
    {
        $this->remove($key);
    }

    public function offsetExists(mixed $key): bool
    {
        $this->initialize();

        return isset($this->data[$key]);
    }

    public function offsetGet(mixed $key) : mixed
    {
        return $this->get($key);
    }

    public function offsetSet(mixed $key, mixed $value): void
    {
        $this->set($key, $value);
    }

    public function offsetUnset(mixed $key): void
    {
        $this->remove($key);
    }

    protected function initialize(): void
    {
        if ($this->initialized === true) {
            return;
        }

        $this->initialized = true;

        $this->doInitialize();
    }

    protected function preparePartValue(string $key, string|AbstractPart|null $value) : mixed
    {
        if (! isset($this->partClassMap[$key])) {
            return $value;
        }

        $className = $this->partClassMap[$key];

        return ! $value instanceof $className ? new $className($value) : $value;
    }

    abstract public function __toString(): string;

    abstract protected function doInitialize(): void;
}
