<?php

namespace App\Http\Controllers;

use App\Events\CallbackQueryReceived;
use App\Events\UrlsAdded;
use App\Http\Requests\TelegramBotRequest;
use App\Models\TelegramUpdate;
use Illuminate\Http\Response;

class TelegramBotController extends Controller
{
    /**
     * Handle Telegram updates
     *
     * @param TelegramBotRequest $request
     * @return Response
     * @throws \JsonException
     */
    public function webhook(TelegramBotRequest $request): Response
    {
        /** @var TelegramUpdate $telegramUpdate */
        $telegramUpdate = TelegramUpdate::create(['payload' => $request->getContent()]);

        if ($telegramUpdate->hasUrl()) {
            UrlsAdded::dispatch($telegramUpdate);
        }

        if ($telegramUpdate->isCallbackQuery()) {
            CallbackQueryReceived::dispatch($telegramUpdate);
        }

        return \response()->noContent(200);
    }
}
