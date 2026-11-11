<?php

return [
    'schedule_timezone' => env('SPORTCENTRUM_TIMEZONE', 'Europe/Amsterdam'),
    'low_occupancy_percent' => (int) env('SPORTCENTRUM_LOW_OCCUPANCY_PERCENT', 50),
];
