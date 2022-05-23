<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\EventService;
class UpdateEventRequest extends FormRequest
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
            'event_name' => ['required', 'max:50'],
            'information' => ['required', 'max:200'],
            'event_date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required', 'after:start_time'],
            'max_people' => ['required', 'numeric', 'between:1,20'],
            'is_visible' => ['required', 'boolean']
        ];
    }

    public function withValidator($validator) {
        if ($validator->fails()) return;
        $validator->after(function ($validator) {
            $eventService = app()->make(EventService::class);
            $check = $eventService->checkEditEventDuplication(
                $this->route()->parameter('event')->id, $this['event_date'], $this['start_time'], $this['end_time']
            );
            if($check){
                $validator->errors()->add('start_time', 'この時間帯は既に他の予約が存在します。');
            }
        });
    }
}
