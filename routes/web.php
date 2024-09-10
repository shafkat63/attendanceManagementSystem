<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('Dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('welcome');
Route::get('getMenu', [\App\Http\Controllers\DashboardController::class, 'test'])->name('welcome');
Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])->name('Dashboard');
Route::get('login', [\App\Http\Controllers\UserConfig\UsersController::class, 'LoginFrom'])->name('login');
Route::post('requestLogin',[\App\Http\Controllers\UserConfig\UsersController::class,'authenticate']);
Route::post('/logout',[\App\Http\Controllers\UserConfig\UsersController::class,'logout'])->name('logout');

Route::resource('SidebarNav', App\Http\Controllers\WebSetup\SidebarNavController::class);
Route::post('/get/all/SidebarNav', [App\Http\Controllers\WebSetup\SidebarNavController::class, 'getData'])->name('all.SidebarNav');


Route::resource('Permission', App\Http\Controllers\UserConfig\PermissionController::class);
Route::post('/get/all/Permission', [App\Http\Controllers\UserConfig\PermissionController::class, 'getData'])->name('all.Permission');

Route::resource('Roles', App\Http\Controllers\UserConfig\RolesController::class);
Route::post('/get/all/Roles', [App\Http\Controllers\UserConfig\RolesController::class, 'getData'])->name('all.Roles');
Route::get('/addpermission/{roleid}', [\App\Http\Controllers\UserConfig\RolesController::class, 'addPermissionToRole']);
Route::post('GivePermissionToRole', [\App\Http\Controllers\UserConfig\RolesController::class, 'GivePermissionToRole']);


Route::resource('User', \App\Http\Controllers\UserConfig\UsersController::class);
Route::post('/get/all/User', [\App\Http\Controllers\UserConfig\UsersController::class, 'getData'])->name('all.User');
Route::get('GetRoles', [\App\Http\Controllers\UserConfig\UsersController::class, 'GetRoles']);
