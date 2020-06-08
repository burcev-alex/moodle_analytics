<?php

declare(strict_types=1);

use App\Orchid\Screens\MoodleAccount\MoodleAccountEditScreen;
use App\Orchid\Screens\MoodleAccount\MoodleAccountListScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', PlatformScreen::class)->name('platform.main');

// Users...
Route::screen('users/{users}/edit', UserEditScreen::class)->name('platform.systems.users.edit');
Route::screen('users', UserListScreen::class)->name('platform.systems.users');

// Roles...
Route::screen('roles/{roles}/edit', RoleEditScreen::class)->name('platform.systems.roles.edit');
Route::screen('roles/create', RoleEditScreen::class)->name('platform.systems.roles.create');
Route::screen('roles', RoleListScreen::class)->name('platform.systems.roles');

$this->router->screen('schedule/{post?}', ScheduleEditScreen::class)
    ->name('platform.schedule.edit');

$this->router->screen('schedules', ScheduleListScreen::class)
    ->name('platform.schedule.list');

$this->router->screen('moodleaccount/{post?}', MoodleUserEditScreen::class)
    ->name('platform.moodleaccount.edit');

$this->router->screen('moodleaccount', MoodleUserListScreen::class)
    ->name('platform.moodleaccount.list');

