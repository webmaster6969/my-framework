<?php

declare(strict_types=1);

namespace Core\Console;

abstract class Command
{
    /**
     * @var string
     */
    protected string $name = '';
    /**
     * @var string
     */
    protected string $description = '';

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param list<string> $arguments
     * @return void
     */
    abstract public function handle(array $arguments): void;
}