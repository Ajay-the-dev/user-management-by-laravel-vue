<?php

use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('student-notice', function ($user) {
    return $user->role === 'STUDENT';
});

Broadcast::channel('staff-notice', function ($user) {
    return $user->role === 'STAFF' || $user->role === 'ADMIN';
});