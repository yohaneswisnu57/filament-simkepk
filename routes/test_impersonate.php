<?php
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/test-impersonate/{id}', function($id) {
    $user = User::find($id);
    auth()->login($user);
    session()->put('impersonated_by', 1);
    session()->regenerate();
    session()->save();
    return redirect('/user');
});

Route::get('/test-dump-session', function() {
    return session()->all();
});
