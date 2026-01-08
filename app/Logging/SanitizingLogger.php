<?php

namespace App\Logging;

use Monolog\Handler\FormattableHandlerInterface;
use Monolog\Logger;

class SanitizingLogger
{
    /**
     * Customize the given logger instance.
     * This can be used via Laravel's 'tap' configuration for standard drivers.
     * Currently, we use the custom driver approach (CreateSanitizedLogger) instead.
     */
    public function __invoke(Logger $logger): void
    {
        foreach ($logger->getHandlers() as $handler) {
            if ($handler instanceof FormattableHandlerInterface) {
                $handler->setFormatter(new SanitizingFormatter(
                    format: "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
                    dateFormat: 'Y-m-d H:i:s',
                    allowInlineLineBreaks: true,
                    ignoreEmptyContextAndExtra: true
                ));
            }
        }
    }
}
