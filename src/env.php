<?php

/**
 * Helper para cargar variables de entorno desde un archivo .env
 */
function loadEnv($path = null)
{
    // Si no se especifica path, buscar en la raíz del proyecto
    if ($path === null) {
        $path = dirname(__DIR__) . '/.env';
    }
    
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Ignorar comentarios y líneas vacías
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }

        // Separar clave y valor
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Remover comillas si existen
            $value = trim($value, '"\'');
            
            // Establecer la variable de entorno solo si no existe ya
            if (!getenv($name)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
    
    return true;
}

// Cargar automáticamente las variables de entorno
loadEnv();
