<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/oauth/redirect', function () {
    return Socialite::driver('discord')->redirect();
})->name('oauth-redirect');

Route::get('/oauth/callback', function () {
    $user = Socialite::driver('discord')->user();

    if ($dbUser = User::firstWhere('discord_id', $user->id)) {
        Auth::login($dbUser);
        return redirect('/telescope');
    }

    return redirect('/', 401);
});
