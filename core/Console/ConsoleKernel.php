<?php

declare(strict_types=1);

namespace Core\Console;

class ConsoleKernel
{
    /**
     * @var array<string, Command>
     */
    protected array $commands = [];

    public function register(Command $command): void
    {
        $this->commands[$command->getName()] = $command;
    }

    /**
     * @param list<string> $arguments
     */
    public function handle(array $arguments): void
    {
        $commandName = $arguments[1] ?? null;
        $arguments = array_slice($arguments, 2); // тип: list<string>

        if (!$commandName || !isset($this->commands[$commandName])) {
            echo "Команда не найдена.\n";
            $this->listCommands();
            return;
        }

        $this->commands[$commandName]->handle($arguments);
    }

    protected function listCommands(): void
    {
        echo "Доступные команды:\n";
        foreach ($this->commands as $name => $command) {
            echo "  $name\t" . $command->getDescription() . "\n";
        }
    }
}