<?php

namespace App\Http\Integrations\TelegramBot\Dtos;

class BotCommand
{
    public const SEPARATOR = '_';

    public function __construct(
        public readonly MessageReference $messageReference,
        private string $command,
        private string $payload = ''
    ) {
    }

    /**
     * @return string
     */
    public function getCommand(): string
    {
        $command = ltrim($this->command, '/');

        if (str_contains($command, self::SEPARATOR)) {
            return explode(self::SEPARATOR, $command)[0];
        }

        return $command;
    }

    /**
     * @return string
     */
    public function getPayload(): string
    {
        if (str_contains($this->command, self::SEPARATOR)) {
            return explode(self::SEPARATOR, $this->command)[1];
        }

        $length = strpos($this->payload, '/');
        if (!$length) {
            $length = null;
        }

        $payload = substr($this->payload, 0, $length);
        return trim($payload);
    }
}
