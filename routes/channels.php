<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('office-presence', function ($user) {
    // When a tab opens, tell everyone which station is currently occupied
    return [
        'id' => $user->id,
        'role' => $user->name 
    ];
});
