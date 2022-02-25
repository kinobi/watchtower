<?php

namespace App\Http\Controllers;

use App\Http\Integrations\TelegramBot\Requests\SendMessageRequest;
use App\Http\Integrations\Txtpaper\Requests\CreateMobiDocumentRequest;
use App\Http\Requests\TelegramBotRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    public function webhook(TelegramBotRequest $request): Response
    {
        // @todo Extract a DTO from the Request
        $payload = (array)json_decode(json: $request->getContent(), associative: true, flags: JSON_THROW_ON_ERROR);

        $chatId = (int)data_get($payload, 'message.chat.id');
        $text = (string)data_get($payload, 'message.text', '');
        $entities = collect(data_get($payload, 'message.entities', []));

        $urlEntity = $entities->firstWhere('type', 'url');
        $commandEntity = $entities->firstWhere('type', 'bot_command');

        // @todo Move to a Job
        if ($urlEntity) {
            $url = mb_substr($text, $urlEntity['offset'], $urlEntity['length']);
            $txtpaperRequest = new CreateMobiDocumentRequest($url, config('services.txtpaper.mobi.email'));
            $txtpaperResponse = $txtpaperRequest->send();
            if ($txtpaperResponse->json('status') === 'success') {
                $botRequest = new SendMessageRequest($chatId, __('watchtower.txtpaper.success'));
                $botRequest->send();
            }
        }

        if ($commandEntity) {
            Log::info('Command received: ' . $text);
        }

        return \response()->noContent(200);
    }
}
