<?php
declare(strict_types=1);
namespace App\Models;

use Carbon\Carbon;

class EventInfo
{
    public function __construct(
        public int $id,
        public string $name,
        public Carbon $start_date,
        public Carbon $end_date,
        public int $number_of_people
    ) {}
}