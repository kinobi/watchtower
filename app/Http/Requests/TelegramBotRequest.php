<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TelegramBotRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $fromId = $this->json('message.from.id') ?? $this->json('callback_query.from.id');

        return $fromId && (int)$fromId === (int)config('services.telegram.user.id');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'update_id' => ['required', 'integer'],
        ];
    }
}
