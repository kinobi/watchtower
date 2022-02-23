<?php

namespace App\Http\Controllers;

use App\Http\Requests\TelegramBotRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    public function webhook(TelegramBotRequest $request): Response|JsonResponse
    {
        $payload = (array)json_decode(json: $request->getContent(), associative: true, flags: JSON_THROW_ON_ERROR);

        $chatId = (int)data_get($payload, 'message.chat.id');
        $text = (string)data_get($payload, 'message.text', '');
        $entities = collect(data_get($payload, 'message.entities', []));

        $urlEntity = $entities->firstWhere('type', 'url');
        $commandEntity = $entities->firstWhere('type', 'bot_command');

        if ($urlEntity) {
            $url = mb_substr($text, $urlEntity['offset'], $urlEntity['length']);

            $txtpaper = Http::get('https://txtpaper.com/api/v1/', [
                'url' => $url,
                'format' => 'mobi',
                'email' => config('services.txtpaper.mobi.email'),
            ]);

            if ($txtpaper->json('status') === 'success') {
                return new JsonResponse([
                    'method' => 'sendMessage',
                    'chat_id' => $chatId,
                    'text' => __('watchtower.txtpaper.success'),
                ]);
            }
        }

        if ($commandEntity) {
            Log::debug('Command received: ' . $text);
        }

        return \response()->noContent(200);
    }
}
