<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('/login', [ AuthController::class, 'authenticate_admin' ]);
    Route::get('/me', [ AuthController::class, 'checkAuthAdmin']);
});

Route::middleware([ 'jwt.verify:admin_api', 'role:admin,admin_api' ])
    ->group(function() {
        
        Route::post('/check', [ AuthController::class, 'adminVerification' ])->name('check-admin');

        Route::get('/permissions', [ AdminController::class, 'getPermissions' ]);
        Route::get('/post_types', [ AdminController::class, 'getPostTypes' ]);
        Route::get('/report_motives', [ AdminController::class, 'getReportMotives' ]);

        Route::prefix('/administrators')
            ->name('administrators.')
            ->group(function() {
                Route::get('/', [ AdminController::class, 'getAdmins' ])->name('index');
                Route::get('/{admin}/show', [ AdminController::class, 'showAdmin' ])->name('show');
                Route::post('/', [ AdminController::class, 'storeAdmin' ])->name('store');
                Route::put('{admin}', [ AdminController::class, 'updateAdmin' ])->name('update');
                Route::delete('/{admin}', [ AdminController::class, 'destroyAdmin' ])->name('destroy');
            });

        Route::prefix('/users')
            ->name('users.')
            ->group(function() {                      
                Route::get('/{type}', 'UserController@getUsers')->name('index');
                Route::get('/{user}/show', 'UserController@show')->name('profile');
                Route::patch('/{user}/block/{fromReports?}', 'UserController@block')->name('block');
                Route::patch('/{user}/unblock', 'UserController@unblock')->name('unblock');
                Route::delete('/{user}', 'UserController@destroy')->name('destroy');
                Route::delete('/{user}/{fromReports?}', 'UserController@destroy')->name('destroy');

                Route::get('/{type}/total-count', 'UserController@countTotal')->name('count-total');
                
                Route::get('/{type}/publishers', 'UserController@getPublishers')->name('index.publishers');
            });

        Route::prefix('/publishers')
            ->name('publishers.')
            ->group(function() {                
                Route::get('/{type}', 'UserController@getPublishers')->name('index');
                Route::get('/{user}/show', [ UserController::class, 'showPublisher' ])->name('show');

                Route::get('/{type}/total-count', 'UserController@countTotal')->name('count-total');
                
                Route::get('/{type}/publishers', 'UserController@getPublishers')->name('index.publishers');
            });

        Route::prefix('/publications')
            ->name('publications.')
            ->group(function() {
                Route::patch('/{publication}/block', 'PublicationController@block')->name('block');
                Route::patch('/{publication}/unblock', 'PublicationController@unblock')->name('unblock');
                Route::delete('/{publication}', 'PublicationController@destroy')->name('delete');
            });

        Route::prefix('/reports')
            ->name('reports.')
            ->group(function() {
                Route::get('/posts/total', [ ReportController::class, 'getTotalPostsReports' ])->name('count');
                Route::get('/posts', [ ReportController::class, 'getPostsReports' ])->name('index');
                Route::get('/reviews/total', [ ReportController::class, 'getTotalReviewsReports' ])->name('count');
                Route::get('/reviews', [ ReportController::class, 'getReviewsReports' ])->name('index');
                Route::get('/{reported}/details', [ ReportController::class, 'showUserReported' ])->name('showUserReported');
                Route::get('/{report}/show', [ ReportController::class, 'showPublicationReported' ])->name('showPublicationReported');
                Route::patch('/{report}/discard', [ ReportController::class, 'discard' ])->name('discard');
                Route::delete('/reviews/{review}', [ ReviewController::class, 'destroy' ])->name('destroy');
            });

        Route::prefix('/unblock_requests')
            ->name('unblock_requests.')
            ->group(function() {
                Route::get('/', 'UnblockRequestController@getUnblockRequests')->name('index');
                Route::get('/{petition}/show', 'UnblockRequestController@show')->name('details');
                Route::patch('/{petition}/reject', 'UnblockRequestController@reject')->name('reject');
                Route::patch('/{petition}/accept', 'UnblockRequestController@accept')->name('accept');
            });
            
        Route::put('/plans/update_status', [ PlanController::class, 'updateStatus' ]);
        Route::resource('plans', PlanController::class)
            ->only([
                'index',
                'update'
            ]);

        Route::prefix('/settings')
            ->name('settings.')
            ->group(function() {
                Route::get('/', [ SettingController::class, 'index' ])->name('index');
                Route::post('/legal_info/{type}', [ SettingController::class, 'storeLegalInfo' ])->name('store.legalInfo');
                Route::put('/update_contact_info', [ SettingController::class, 'updateContactInfo' ])->name('store.contactInfo');
            });
            
});