<?php

return [
    /*
    | When true, employees can submit/edit daily reports at any time (for testing).
    | When false, morning/evening time windows (IST) are enforced.
    */
    'ignore_time_window' => env('DAILY_REPORT_IGNORE_TIME', false),

    /*
    | Employees can edit their report only within this many days after the report date.
    | 1 = today or yesterday only.
    */
    'employee_edit_days' => (int) env('DAILY_REPORT_EDIT_DAYS', 1),
];
