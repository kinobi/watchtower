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
        $fromId = (int)collect([
            $this->json('message.from.id'),
            $this->json('callback_query.from.id'),
            $this->json('edited_message.from.id')
        ])
            ->filter()
            ->sole();

        return $fromId && in_array(
                $fromId,
                [(int)config('services.telegram.user.id'), (int)config('services.telegram.bot.id')],
                true
            );
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
