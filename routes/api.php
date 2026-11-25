<?php

use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\UserCollectionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - FlipPlay REST API (Modul 4)
|--------------------------------------------------------------------------
| Seluruh endpoint didaftarkan di sini dan dapat diverifikasi melalui:
| php artisan route:list
|
*/

// 1. Endpoint CRUD Standar Entitas Media (index, show, store, update, destroy)
Route::apiResource('media', MediaController::class);

// 2. Endpoint Pengguna (Daftar Pengguna & Profil)
Route::get('users', [UserCollectionController::class, 'index']);
Route::get('users/{user}', [UserCollectionController::class, 'show']);

// 3. Custom Actions: Manajemen Relasi Koleksi Media Pengguna (belongsToMany)
// - Menampilkan koleksi media milik pengguna
Route::get('users/{user}/collection', [UserCollectionController::class, 'getCollection']);
// - Menambahkan media ke koleksi pengguna (attach)
Route::post('users/{user}/collection', [UserCollectionController::class, 'attachMedia']);
// - Menghapus media dari koleksi pengguna (detach)
Route::delete('users/{user}/collection/{mediaId}', [UserCollectionController::class, 'detachMedia']);
