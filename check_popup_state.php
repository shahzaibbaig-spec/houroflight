<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$now = now()->toDateTimeString();
$live = App\Models\Announcement::query()->live()->latest('id')->first();
$active = App\Models\Announcement::query()->where('is_active',true)->latest('id')->first();
echo 'now='.$now.PHP_EOL;
echo 'live=' . ($live ? $live->id : 'none') . PHP_EOL;
echo 'active=' . ($active ? $active->id : 'none') . PHP_EOL;
if($active){echo 'active_window=' . ($active->start_at ?? 'null') . ' -> ' . ($active->end_at ?? 'null') . PHP_EOL;}
