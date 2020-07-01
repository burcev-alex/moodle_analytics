<?php

declare(strict_types=1);

use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\Moodle\AccountEditScreen;
use App\Orchid\Screens\Moodle\AccountListScreen;
use App\Orchid\Screens\Moodle\CourseEditScreen;
use App\Orchid\Screens\Moodle\CourseListScreen;
use App\Orchid\Screens\Queue\LsaListScreen;
use App\Orchid\Screens\ResultComparison;
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

// Moodle Account
$this->router->screen('moodleaccount/{post?}', AccountEditScreen::class)->name('platform.moodleaccount.edit');
$this->router->screen('moodleaccounts', AccountListScreen::class)->name('platform.moodleaccount.list');

// Moodle курсы
$this->router->screen('moodlecourse/{post?}', CourseEditScreen::class)->name('platform.moodlecourse.edit');
$this->router->screen('moodlecourses', CourseListScreen::class)->name('platform.moodlecourse.list');
$this->router->screen('queue-lsa', LsaListScreen::class)->name('platform.lsa.list');

// LSA Result comparison
$this->router->screen('lsaresultcomparison/{post?}', ResultComparison\LsaEditScreen::class)->name('platform.lsaresultcomparison.edit');
$this->router->screen('lsaresultcomparisons', ResultComparison\LsaListScreen::class)->name('platform.lsaresultcomparison.list');

