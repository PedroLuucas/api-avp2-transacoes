<?php
namespace App\Utils;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

class SimpleContainer implements ContainerInterface
{
    private array $services = [];

    public function set(string $id, callable $callable): void
    {
        $this->services[$id] = $callable;
    }

    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new class($id) extends \Exception implements NotFoundExceptionInterface {
                public function __construct(string $id) {
                    parent::__construct("Service '$id' not found in container.");
                }
            };
        }

        if (is_callable($this->services[$id])) {
            return ($this->services[$id])();
        }

        return $this->services[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}
