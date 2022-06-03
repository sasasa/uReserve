<?php
declare(strict_types=1);
namespace App\Models;

use Carbon\Carbon;

class ReservedInfo
{
    public function __construct(
        public string $name,
        public int $number_of_people,
        public Carbon $canceled_date,
    ) {}
}