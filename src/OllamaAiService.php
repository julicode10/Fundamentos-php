<?php

namespace App;

use ArdaGnsrn\Ollama\Ollama;

class OllamaAiService
{
    protected $client;

    public function __construct()
    {
        $this->client = Ollama::client();
    }   

    public function getResponse(string $question): string
    {
        $result = $this->client->chat()
            ->create([
                'model' => 'deepseek-r1:1.5b',
                'messages' => [
                    ['role' => 'user', 'content' => $question],
                ]
            ]);
        
        return $result->message->content;
    }
}
