<?php

use Illuminate\Support\Facades\Route;

$controller = config('media-library.routes.controller');

Route::get('stats', [$controller, 'stats'])->name('stats');
Route::post('batch-delete', [$controller, 'batchDestroy'])->name('batch-destroy');
Route::get('{medium}/download', [$controller, 'download'])->name('download');
Route::get('', [$controller, 'index'])->name('index');
Route::post('', [$controller, 'store'])->name('store');
Route::get('{medium}', [$controller, 'show'])->name('show');
Route::match(['put', 'patch'], '{medium}', [$controller, 'update'])->name('update');
Route::delete('{medium}', [$controller, 'destroy'])->name('destroy');
