<?php

use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/blogs', [BlogController::class, 'list']);
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route::middleware(['auth'])->group(function () {
//     Route::resource('admin/blogs', BlogController::class);
// });
Route::middleware(['auth'])->group(function () {
    // Blog Management Routes
    Route::get('/admin/blogs', [BlogController::class, 'index'])->name('blogs.index'); // Show blog listing
    Route::get('/admin/blogs/create', [BlogController::class, 'create'])->name('blogs.create'); // Show create form
    Route::post('/admin/blogs', [BlogController::class, 'store'])->name('blogs.store'); // Store new blog
    Route::get('/admin/blogs/{id}/edit', [BlogController::class, 'edit'])->name('blogs.edit'); // Show edit form
    Route::put('/admin/blogs/{id}', [BlogController::class, 'update'])->name('blogs.update'); // Update blog
    Route::delete('/admin/blogs/{id}', [BlogController::class, 'destroy'])->name('blogs.destroy'); // Delete blog
});
