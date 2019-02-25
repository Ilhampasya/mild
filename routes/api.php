<?php

Route::get('user', function () {
    return response()->json([
        'response' => true,
        'message' => 'hello world!',
    ]);
});
