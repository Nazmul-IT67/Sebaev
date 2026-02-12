<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Backend\CmsController;
use App\Http\Controllers\Web\Backend\FaqController;
use App\Http\Controllers\Web\Backend\UserController;
use App\Http\Controllers\Web\Backend\ReportController;
use App\Http\Controllers\Web\Backend\BannerController;
use App\Http\Controllers\Web\Backend\CommentController;
use App\Http\Controllers\Web\Backend\CategoryController;
use App\Http\Controllers\Web\Backend\UserRoleController;
use App\Http\Controllers\Web\Backend\MovementController;
use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\SubCategoryController;
use App\Http\Controllers\Web\Backend\MovementVideoController;
use App\Http\Controllers\Web\Backend\UserPermissionController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

// FAQ Routes
Route::controller(FaqController::class)->group(function () {
    Route::get('/faqs', 'index')->name('admin.faqs.index');
    Route::get('/faqs/create', 'create')->name('admin.faqs.create');
    Route::post('/faqs/store', 'store')->name('admin.faqs.store');
    Route::get('/faqs/edit/{id}', 'edit')->name('admin.faqs.edit');
    Route::post('/faqs/update/{id}', 'update')->name('admin.faqs.update');
    Route::post('/faqs/status/{id}', 'status')->name('admin.faqs.status');
    Route::post('/faqs/destroy/{id}', 'destroy')->name('admin.faqs.destroy');
});

Route::controller(CategoryController::class)->group(function () {
    Route::get('/category', 'index')->name('admin.categories.index');
    Route::post('/category/store', 'store')->name('admin.categories.store');
    Route::get('/category/edit/{id}', 'edit')->name('admin.categories.edit');
    Route::post('/category/update/{id}', 'update')->name('admin.categories.update');
    Route::delete('/category/destroy/{id}', 'destroy')->name('admin.categories.destroy');
    Route::post('/category/status/{id}', 'status')->name('admin.categories.status');
    Route::post('/category-status/{id}', 'categoryStatus')->name('admin.categories.category.status');
});

Route::controller(SubCategoryController::class)->group(function () {
    Route::get('/subcategory', 'index')->name('admin.subcategory.index');
    Route::post('/subcategory/store', 'store')->name('admin.subcategory.store');
    Route::get('/subcategory/create', 'create')->name('admin.subcategory.create');
    Route::get('/subcategory/edit/{id}', 'edit')->name('admin.subcategory.edit');
    Route::post('/subcategory/update{id}', 'update')->name('admin.subcategory.update');
    Route::delete('/subcategory/destroy/{id}', 'destroy')->name('admin.subcategory.destroy');
    Route::get('/subcategory/status/{id}', 'status')->name('admin.subcategory.status');
});

Route::controller(BannerController::class)->group(function () {
    Route::get('/banner', 'index')->name('admin.banner.index');
    Route::post('/banner/store', 'store')->name('admin.banner.store');
    Route::get('/banner/create', 'create')->name('admin.banner.create');
    Route::get('/banner/edit/{id}', 'edit')->name('admin.banner.edit');
    Route::post('/banner/update{id}', 'update')->name('admin.banner.update');
    Route::delete('/banner/destroy/{id}', 'destroy')->name('admin.banner.destroy');
    Route::post('/banner/status/{id}', 'status')->name('admin.banner.status');
});

Route::controller(ReportController::class)->group(function () {
    Route::get('/report', 'index')->name('admin.report.index');
    Route::get('/report/{id}/show', 'show')->name('admin.report.show');
    Route::delete('/report/destroy/{id}', 'destroy')->name('admin.report.destroy');
    Route::delete('/movement/{id}', 'destroy_movement')->name('admin.report.destroy_movement');
    Route::delete('/post/{id}', 'destroy_post')->name('admin.report.destroy_post');
});

Route::controller(CmsController::class)->group(function () {
    Route::get('/duration', 'index')->name('admin.duration.index');
    Route::post('/duration/update', 'update')->name('admin.duration.update');
});

Route::prefix('/users')->controller(UserController::class)->group(function () {
    Route::get('/index', 'index')->name('admin.users.index');
    Route::get('/show/{id}', 'show')->name('admin.users.show');
    Route::get('/create', 'create')->name('admin.users.create');
    Route::get('/edit/{id}', 'edit')->name('admin.users.edit');
    Route::post('/store', 'store')->name('admin.users.store');
    Route::post('/update', 'update')->name('admin.users.update');
    Route::get('/status/{id}', 'status')->name('admin.users.status');
    Route::delete('/destroy/{id}', 'destroy')->name('admin.users.destroy');
});

Route::prefix('/movements')->controller(MovementController::class)->group(function () {
    Route::get('/index', 'index')->name('admin.movements.index');
    Route::get('/document/{id}', 'show')->name('admin.movements.document');
    Route::delete('/destroy/{id}', 'destroy')->name('admin.movements.destroy');
    Route::get('/status/{id}', 'status')->name('admin.movements.status');
});

Route::prefix('/movement/video')->controller(MovementVideoController::class)->group(function () {
    Route::get('/index', 'index')->name('admin.movement_video.index');
    Route::get('/status/{id}', 'status')->name('admin.movement_video.status');
    Route::delete('/destroy/{id}', 'destroy')->name('admin.movement_video.destroy');
});

Route::prefix('/comments')->controller(CommentController::class)->group(function () {
    Route::get('/index', 'index')->name('admin.comments.index');
    // Route::get('/status/{id}', 'status')->name('admin.comments.status');
    // Route::delete('/destroy/{id}', 'destroy')->name('admin.comments.destroy');
});

Route::prefix('/roles')->controller(UserRoleController::class)->group(function () {
    Route::get('/index', 'index')->name('admin.roles.index');
    Route::get('/create', 'create')->name('admin.roles.create');
    Route::get('/edit/{id}', 'edit')->name('admin.roles.edit');
    Route::post('/store', 'store')->name('admin.roles.store');
    Route::post('/update/{id}', 'update')->name('admin.roles.update');
    Route::delete('/destroy/{id}', 'destroy')->name('admin.roles.destroy');
});

Route::prefix('/permissions')->controller(UserPermissionController::class)->group(function () {
    Route::get('/index', 'index')->name('admin.permissions.index');
    Route::get('/create', 'create')->name('admin.permissions.create');
    Route::get('/edit/{id}', 'edit')->name('admin.permissions.edit');
    Route::post('/store', 'store')->name('admin.permissions.store');
    Route::post('/update/{id}', 'update')->name('admin.permissions.update');
    Route::delete('/destroy/{id}', 'destroy')->name('admin.permissions.destroy');
});
