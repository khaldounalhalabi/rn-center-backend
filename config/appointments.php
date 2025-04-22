<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Appointment Duration
    |--------------------------------------------------------------------------
    |
    | This value represents the default duration of an appointment in minutes.
    | This is used for calculating available time slots in a clinic's schedule.
    |
    */
    'duration_minutes' => env('APPOINTMENT_DURATION_MINUTES', 30),

    /*
    |--------------------------------------------------------------------------
    | Minimum Appointment Gap
    |--------------------------------------------------------------------------
    |
    | This value represents the minimum gap between appointments in minutes.
    | This ensures there's a buffer between appointments for doctor preparation.
    |
    */
    'min_gap_minutes' => env('APPOINTMENT_MIN_GAP_MINUTES', 0),
];
