<?php

namespace App\Logging;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class CreateSanitizedLogger
{
    /**
     * Create a custom Monolog instance with PII sanitization.
     */
    public function __invoke(array $config): Logger
    {
        $logger = new Logger($config['name'] ?? 'sanitized');

        // Determine handler based on config
        $path = $config['path'] ?? storage_path('logs/laravel.log');
        $level = $this->parseLevel($config['level'] ?? 'debug');

        if (isset($config['days']) && $config['days'] > 0) {
            $handler = new RotatingFileHandler($path, $config['days'], $level);
        } else {
            $handler = new StreamHandler($path, $level);
        }

        // Apply sanitizing formatter
        $handler->setFormatter(new SanitizingFormatter(
            format: "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
            dateFormat: 'Y-m-d H:i:s',
            allowInlineLineBreaks: true,
            ignoreEmptyContextAndExtra: true
        ));

        $logger->pushHandler($handler);

        return $logger;
    }

    /**
     * Parse log level string to Monolog Level enum.
     */
    protected function parseLevel(string $level): Level
    {
        return Logger::toMonologLevel($level);
    }
}
