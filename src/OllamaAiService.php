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
        try {
            $result = $this->client->chat()
                ->create([
                    'model' => 'deepseek-r1:1.5b',
                    'messages' => [
                        ['role' => 'user', 'content' => $question],
                    ]
                ]);
            
            return $result->message->content ?? 'No response from AI.';
            
        } catch (\Exception $e) {
            
            // Retornar un mensaje de error amigable al usuario
            return 'Lo siento, hubo un error al procesar tu consulta. Por favor, inténtalo nuevamente. Error: ' . $e->getMessage();
        }
        
    }
}
