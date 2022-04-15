<?php

namespace App\Listeners;

use App\Events\BotCommandReceived;
use App\Http\Integrations\TelegramBot\Dtos\BotCommand;
use App\Support\Command;
use Error;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ExecuteBotCommand implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(BotCommandReceived $event): void
    {
        $telegramUpdate = $event->telegramUpdate;
        $botCommands = $telegramUpdate->getBotCommands();

        $botCommands
            ->each(
                fn(BotCommand $botCommand) => $this->executeCommand($botCommand)
            );
    }

    protected function executeCommand(BotCommand $botCommand): void
    {
        try {
            $command = Command::from($botCommand->getCommand());
            $command->execute($botCommand->messageReference, $botCommand->getPayload());
        } catch (Error) {
            Log::error('Unsupported Bot Command', [
                'command' => $botCommand->getCommand(),
                'payload' => $botCommand->getPayload(),
            ]);
        }
    }
}
