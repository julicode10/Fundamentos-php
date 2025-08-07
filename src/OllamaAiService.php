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
                        ['role' => 'system', 'content' => <<<EOT
                            Actúa como un asistente técnico enfocado exclusivamente en PHP.
                            - Tu objetivo es proporcionar respuestas claras, concisas y directamente útiles para desarrolladores, evitando información irrelevante.
                            - Si la consulta está relacionada con PHP, responde con precisión técnica en menos de 120 palabras, incluyendo ejemplos si son útiles.
                            - Si la consulta no está relacionada con PHP, responde: "Lo siento, este asistente está diseñado exclusivamente para consultas sobre PHP." ⚙️ Siempre prioriza buenas prácticas, compatibilidad con PHP 8.2+, y contextualiza según CLI, web, u otras tecnologías si se especifican.
                            EOT
                        ],
                        ['role' => 'user', 'content' => $question],
                    ]
                ]);
            
            $content = $result->message->content ?? 'No response from AI.';
            
            // Filtrar el pensamiento del modelo DeepSeek R1
            return $this->filterThinking($content);
            
        } catch (\Exception $e) {

            
            // Retornar un mensaje de error amigable al usuario
            return 'Lo siento, hubo un error al procesar tu consulta. Por favor, inténtalo nuevamente. Error: ' . $e->getMessage();
        }
        
    }

    /**
     * Filtra el "pensamiento" del modelo DeepSeek R1 y retorna solo la respuesta final
     */
    private function filterThinking(string $content): string
    {
        // El pensamiento suele estar entre <think> y </think> o marcadores similares
        // También puede estar al inicio antes de la respuesta real
        
        // Remover bloques de pensamiento entre etiquetas
        $content = preg_replace('/<think>.*?<\/think>/s', '', $content);
        $content = preg_replace('/<thinking>.*?<\/thinking>/s', '', $content);
        
        // Remover líneas que indican pensamiento interno
        $lines = explode("\n", $content);
        $filteredLines = [];
        $skipThinking = false;
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            // Detectar inicio de pensamiento
            if (preg_match('/^(Let me think|I need to|I should|Let me analyze|First, I|I\'ll)/i', $line)) {
                $skipThinking = true;
                continue;
            }
            
            // Detectar final de pensamiento y inicio de respuesta
            if (preg_match('/^(Here\'s|The answer|To|In PHP|For PHP|You can)/i', $line)) {
                $skipThinking = false;
                $filteredLines[] = $line;
                continue;
            }
            
            // Si no estamos en modo pensamiento, incluir la línea
            if (!$skipThinking && !empty($line)) {
                $filteredLines[] = $line;
            }
        }
        
        $result = implode("\n", $filteredLines);
        
        // Limpiar espacios extra
        $result = trim($result);
        
        return !empty($result) ? $result : 'No se pudo procesar la respuesta.';
    }
}
