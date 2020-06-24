<?php

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('live-stream.{postId}', function (Request $request, $user, $postId) {
    $socketId = $request->header('X-Socket-Id');
    logger("Presence Live Stream", ['request' => $socketId]);

    return ['user_id' => $user->id, 'username' => $user->username, 'socketId' => $socketId];
});
