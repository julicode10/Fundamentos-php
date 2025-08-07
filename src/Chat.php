<?php

namespace App;

class Chat
{
    
    public function __construct(
        private AIServiceInterface $aiService
    )
    {
    }

    public function start()
    {
        $this->displayWelcomeMessage();

        while ($input = $this->prompt()) {
            

            if ($this->exit($input)) {
                break;
            }

            $response = $this->aiService->getResponse($input);
            
            $this->displayResponse($response);
        }
    }

    private function displayWelcomeMessage(): void
    {
        echo 'Welcome to the PHP AI Chat! Ask anything about PHP.' . PHP_EOL;
    }

    private function displayResponse(string $response): void
    {
        echo $response . PHP_EOL;
    }

    private function exit(string $input): bool
    {
        $exitCommands = ['exit', 'quit', 'bye', 'salir'];
        return in_array(strtolower(trim($input)), $exitCommands);
    }

    private function prompt(): ?string
    {
        return readline('> ');
    }
}
