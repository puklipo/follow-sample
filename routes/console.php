<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('relation', function () {
    $user1 = User::find(1);
    $user2 = User::find(2);
    $user3 = User::find(3);

    dump($user1->followings->count());
    dump($user1->followers->count());
    dump($user2->followings->count());
    dump($user2->followers->count());
    dump($user3->followings->isEmpty());
});
