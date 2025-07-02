<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Job Matching Weights
    |--------------------------------------------------------------------------
    |
    | These weights determine the importance of different factors in the job
    | matching algorithm. The values should be decimals that sum up to 1.0.
    |
    | - skills: How much weight to give the alignment of job and candidate skills.
    | - salary: How much weight to give the salary range compatibility.
    | - location: How much weight to give the location preference match.
    |
    */
    'weights' => [
        'skills' => 0.5,
        'salary' => 0.3,
        'location' => 0.2,
    ],
];
