<?php

namespace App;

use OpenAI;

class OpenAiService
{
    protected $client;

    public function __construct()
    {
        $this->client = OpenAI::client($_ENV['OPENAI_API_KEY']);
    }

    public function getResponse(string $question): string
    {
        try {
            $result = $this->client->chat()
                ->create([
                    'model' => 'gpt-4o-mini' , //'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'user', 'content' => $question],
                    ]
                ]);
            
            return $result->choices[0]->message->content ?? 'No response from AI.';
            
        } catch (\Exception $e) {
        
            
            // Retornar un mensaje de error amigable al usuario
            return 'Lo siento, hubo un error al procesar tu consulta. Por favor, inténtalo nuevamente. Error: ' . $e->getMessage();
        }
    }
}
