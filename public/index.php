<?php

$app = require __DIR__ . '/../bootstrap.php';

$question = $_POST['question'] ?? '';
$answer = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $question) {
    $answer = $app->getResponse($question); 
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistente PHP con IA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'php': '#22C55E',
                        'php-dark': '#16A34A'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen py-8">
    <div class="container mx-auto max-w-4xl px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-php mb-2">🌱 Asistente PHP</h1>
            <p class="text-gray-600 text-lg">Pregunta cualquier cosa sobre PHP y obtén respuestas inteligentes</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-xl shadow-xl p-8 mb-8">
            <form method="POST" class="space-y-6">
                <div>
                    <label for="question" class="block text-sm font-semibold text-gray-700 mb-3">
                        💬 ¿Qué quieres saber sobre PHP?
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            name="question" 
                            id="question"
                            value="<?= htmlspecialchars($question); ?>" 
                            placeholder="Ej: ¿Cómo crear una clase en PHP 8?"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-php focus:outline-none focus:ring-2 focus:ring-php/20 transition-all duration-200 text-gray-700 placeholder-gray-400"
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-php to-php-dark hover:from-php-dark hover:to-php text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-php/50 shadow-lg hover:shadow-xl"
                >
                    <span class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <span>Enviar Pregunta</span>
                    </span>
                </button>
            </form>
        </div>

        <!-- Answer Section -->
        <?php if ($answer): ?>
        <div class="bg-white rounded-xl shadow-xl p-8 animate-fadeIn">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">💡 Respuesta:</h3>
                    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-php">
                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap"><?= htmlspecialchars($answer); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="text-center mt-12 text-gray-500 text-sm space-y-2">
            <p>Construido con 💜 para desarrolladores PHP</p>
            <div class="flex items-center justify-center space-x-2">
                <span>Creado por</span>
                <a 
                    href="https://github.com/julicode10" 
                    target="_blank" 
                    rel="noopener noreferrer"
                    class="inline-flex items-center space-x-1 text-php hover:text-php-dark transition-colors duration-200 font-medium"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                    </svg>
                    <span>Julian Londoño Raigosa</span>
                </a>
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</body>
</html>