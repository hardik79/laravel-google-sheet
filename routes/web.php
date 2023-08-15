<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleSheetController;

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

Route::get('/sheet-form', [GoogleSheetController::class, 'showForm'])->name('show-form');
Route::post('/update-google-sheet', [GoogleSheetController::class, 'updateSheet'])->name('submit-form');

Route::get('/', function () {
    return redirect('sheet-form');
})->name('dashboard');

Route::get('/oauth-callback', [GoogleSheetController::class, 'oauthCallback']);
Route::get('/update-google-sheet', [GoogleSheetController::class, 'updateSheet'])->name('update-google-sheet');


Route::get('/home', function () {
    return view('home');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
