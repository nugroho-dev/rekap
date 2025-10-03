<?php

return [
    // Number of days per scheduled job when splitting large date ranges
    'sync_max_days' => env('SICANTIK_SYNC_MAX_DAYS', 7),

    // Number of rows to upsert per DB chunk
    'sync_batch_size' => env('SICANTIK_SYNC_BATCH_SIZE', 500),

    // If a job receives more than this many items from the API, log a warning and consider splitting further
    'warn_items_threshold' => env('SICANTIK_WARN_ITEMS_THRESHOLD', 50000),
    // When a job receives more than warn_items_threshold items, split the date range into
    // this many days per auto-split job and dispatch them instead of processing in this job.
    'auto_split_days' => env('SICANTIK_AUTO_SPLIT_DAYS', 1),
];
