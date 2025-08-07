<?php

namespace App;

use OpenAI;

// Cargar variables de entorno
require_once __DIR__ . '/env.php';


class OpenAiService
{
    protected $client;

    public function __construct()
    {
        try {
            $apiKey = getenv('OPENAI_API_KEY') ?: $_ENV['OPENAI_API_KEY'] ?? null;
            
            if (!$apiKey) {
                throw new \Exception('OPENAI_API_KEY no está configurada en las variables de entorno');
            }
            
            $this->client = OpenAI::client($apiKey);
        } catch (\Exception $e) {
            error_log('Error al inicializar cliente OpenAI: ' . $e->getMessage());
            throw new \Exception('No se pudo inicializar el servicio de IA: ' . $e->getMessage());
        }
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
