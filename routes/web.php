<?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
 
//Route::get('/', function () {
//    return view('welcome');
//});
 
Route::get('/', [EmployeeController::class, 'index']);
Route::get('/fetchall', [EmployeeController::class, 'fetchAll']);
Route::post('/store', [EmployeeController::class, 'store'])->name('store');
Route::get('/edit', [EmployeeController::class, 'edit'])->name('edit');
Route::post('/update', [EmployeeController::class, 'update'])->name('update');
Route::delete('/delete', [EmployeeController::class, 'delete'])->name('delete');