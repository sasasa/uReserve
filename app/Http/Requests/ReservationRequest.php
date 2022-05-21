<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\ReservationService;

class ReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'reserved_people' => ['required', 'numeric'],
        ];
    }

    public function withValidator($validator) {
        if ($validator->fails()) return;
        $validator->after(function ($validator) {
            $reservationService = app()->make(ReservationService::class);
            if(!$reservationService->canReserve($this->route()->parameter('event'), $this->reserved_people)){
                $validator->errors()->add('予約人数', 'この人数は予約できません。');
            }
        });
    }
}
