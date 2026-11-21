<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private channel khusus dashboard Admin CargoTrace.
Broadcast::channel('admin.dashboard', function ($user) {
    return $user->is_active && $user->isAdmin();
});
