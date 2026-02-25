<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$all = App\Models\Announcement::query()->orderByDesc('id')->get(['id','title','media_type','is_active','start_at','end_at']);
foreach($all as $a){echo $a->id.'|'.$a->media_type.'|'.($a->is_active?'1':'0').'|'.($a->start_at ?? 'null').'|'.($a->end_at ?? 'null').PHP_EOL;}
$live = App\Models\Announcement::query()->live()->latest('id')->first();
echo 'LIVE=' . ($live? $live->id : 'none') . PHP_EOL;
