<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LivewireTestController;
use App\Http\Controllers\AlpineTestController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\SampleController;
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
    return view('calendar');
});

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified'
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');
// });



Route::controller(LivewireTestController::class)->prefix('livewire-test')->name("livewire-test.")->group(function(){
    Route::get('index', 'index')->name('index');
    Route::get('register', "register")->name('register');
});
Route::get('alpine-test/index', [AlpineTestController::class, 'index']); 

Route::get('sample', [SampleController::class, 'index'])->name('sample'); 
Route::get('sample2', [SampleController::class, 'sample2'])->name('sample2'); 
Route::post('sample2', [SampleController::class, 'sample2'])->name('sample2'); 
Route::get('sample3', [SampleController::class, 'sample3'])->name('sample3');
Route::get('sample4', [SampleController::class, 'sample4'])->name('sample4');
Route::post('sample/cities', [SampleController::class, 'cities'])->name('cities');
Route::post('sample/streets', [SampleController::class, 'streets'])->name('streets');
Route::post('sample/address', [SampleController::class, 'address'])->name('address');


Route::prefix('manager')->middleware(['auth', 'can:manager-higher'])->group(function(){
    Route::get('events/past', [EventController::class, 'past'])->name('events.past');
    Route::resource('events', EventController::class);
});
Route::middleware(['auth', 'can:user-higher'])->group(function(){
    Route::get('/mypage', [MyPageController::class, 'index'])->name('mypage.index');
    Route::get('/mypage/{event}', [MyPageController::class, 'show'])->name('mypage.show');
    Route::post('/mypage/{event}', [MyPageController::class, 'cancel'])->name('mypage.cancel');
    Route::get('/dashboard', [ReservationController::class, 'dashboard' ])->name('dashboard');
    Route::post('/{event}', [ReservationController::class, 'reserve'] )->name('events.reserve');
    Route::get('/{event}', [ReservationController::class, 'detail'] )->name('events.detail');
});
