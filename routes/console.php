<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Keep stock alerts / auto-reorder drafts fresh even if nobody touches
// the UI. Controller actions that change stock-relevant state also call
// this directly, so this schedule is a safety net, not the only trigger.
Schedule::command('stock:check-levels')->everyFiveMinutes();
