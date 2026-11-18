<?php

return [
    'departure_delay_threshold_minutes' => (int) env(
        'CARGOTRACE_DEPARTURE_DELAY_THRESHOLD_MINUTES',
        60
    ),
];
