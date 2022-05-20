<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Services\EventService;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    private function getDate()
    {
        // 10時～20時 30分単位
        $availableHour = $this->faker->numberBetween(10, 18); //10時～18時
        $minutes = [0, 30]; // 00分か 30分
        $mKey = array_rand($minutes); //ランダムにキーを取得
        $addHour = $this->faker->numberBetween(1, 3); // イベント時間 1時間～3時間
        $dummyDate = $this->faker->dateTimeThisMonth; // 今月分をランダムに取得
        $startDate = $dummyDate->setTime($availableHour, $minutes[$mKey]);
        $clone = clone $startDate; // そのままmodifyするとstartDateも変わるためコピー
        $endDate = $clone->modify('+'.$addHour.'hour');
        return [$startDate, $endDate];
    }
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        
        [$startDate, $endDate] = $this->getDate();
        // $eventService = new EventService();
        // while($eventService->checkEventDuplication($startDate->format('Y-m-d'), $startDate->format('H:i'), $endDate->format('H:i'))) {
        //     [$startDate, $endDate] = $this->getDate();
        //     dd($startDate, $endDate);
        // }

        return [
            'name' => $this->faker->name,
            'information' => $this->faker->realText,
            'max_people' => $this->faker->numberBetween(2,20),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_visible' => $this->faker->boolean
        ];
    }
}
