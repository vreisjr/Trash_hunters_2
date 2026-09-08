<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComentarioController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::post('/posts/{post}/comentarios', [ComentarioController::class, 'store'])->name('comentarios.store');
    Route::patch('/comentarios/{comentario}', [ComentarioController::class, 'update'])->name('comentarios.update');
    Route::delete('/comentarios/{comentario}', [ComentarioController::class, 'destroy'])->name('comentarios.destroy');
    Route::post('/comentarios/{comentario}/curtir', [ComentarioController::class, 'toggleLike'])->name('comentarios.curtir');
    Route::prefix('posts')->name('posts.')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('index');
        Route::get('/create', [PostController::class, 'create'])->name('create');
        Route::post('/', [PostController::class, 'store'])->name('store');
    });
});

require __DIR__.'/settings.php';
