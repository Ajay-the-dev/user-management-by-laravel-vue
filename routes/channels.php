<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('student-notice-{batchId}', function ($user, $batchId) {
    return $user->role === 'STUDENT' && $user->batchId == $batchId;
});

Broadcast::channel('student-notice', function ($user) {
    return $user->role === "STUDENT";
    // return true; //for triuble shooting
});

Broadcast::channel('staff-notice', function ($user) {
    return $user->role === 'STAFF' || $user->role === 'ADMIN';
});