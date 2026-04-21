<?php

return [

    /*
    | Read-only schedule reference for the My Day screen (CEO).
    | Track drives color: aurateria, main_client, partner, break, other
    */
    'time_blocks' => [
        ['start' => '9:00', 'end' => '9:15', 'label' => 'Morning Report + Plan', 'track' => 'aurateria'],
        ['start' => '9:15', 'end' => '10:15', 'label' => 'Outreach (Aurateria Sales)', 'track' => 'aurateria'],
        ['start' => '10:15', 'end' => '12:30', 'label' => 'Deep Work (Aurateria Delivery)', 'track' => 'aurateria'],
        ['start' => '12:30', 'end' => '13:30', 'label' => 'Lunch + English Practice', 'track' => 'break'],
        ['start' => '13:30', 'end' => '14:00', 'label' => 'Main Client', 'track' => 'main_client'],
        ['start' => '14:00', 'end' => '16:30', 'label' => 'Deep Work (Aurateria Delivery)', 'track' => 'aurateria'],
        ['start' => '16:30', 'end' => '17:00', 'label' => 'Break + Team Check-in', 'track' => 'break'],
        ['start' => '17:00', 'end' => '17:30', 'label' => 'Outreach Block 2', 'track' => 'aurateria'],
        ['start' => '17:30', 'end' => '18:00', 'label' => 'Main Client', 'track' => 'main_client'],
        ['start' => '18:00', 'end' => '19:00', 'label' => 'Partner Projects', 'track' => 'partner'],
        ['start' => '19:00', 'end' => '19:15', 'label' => 'Evening Report', 'track' => 'aurateria'],
    ],

];
