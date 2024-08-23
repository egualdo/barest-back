<?php

use App\Http\Controllers\ReportController;
use App\Http\Controllers\AnswerPostulationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IdiomController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PaymentTypePostController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\Pdf\SuscriptionController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//  Route::post('register', 'App\Http\Controllers\UserController@register');
//     Route::post('login', 'App\Http\Controllers\UserController@authenticate');

// Route::group([
//     'middleware' => ['jwt.verify']
//     ], function() {

//     Route::post('user','App\Http\Controllers\UserController@getAuthenticatedUser');

// // });

//==================================================================================
Route::prefix('auth')->group(function () {
    Route::post('register', 'AuthController@register');
    Route::post('registerFromRRSS', 'AuthController@registerFromRRSS'); //TODO Falta documentar
    Route::post('login', 'AuthController@login');
    Route::post('requestResetPasswordEmail', 'AuthController@sendResetPasswordEmail');
    Route::post('resetNewPassword', 'AuthController@resetNewPassword');
    Route::post('verifyTokenIsCorrect', 'AuthController@verifyTokenIsCorrect');
    Route::post('searchUsersByPhoneNumber', 'AuthController@searchUsersByPhoneNumber');
    Route::post('requestSms', 'AuthController@requestSms');
    Route::post('reactivate/{user}', 'AuthController@reactivate');
    Route::post('users/validateEmail','AuthController@checkEmail');
    Route::post('refresh', 'AuthController@refresh')->middleware(['jwt.verify']);
});

Route::get('categoryAll', 'CategoryController@all');
Route::get('allgroups', 'CategoryController@allgroups');
Route::get('allSubCategories', 'CategoryController@allSubCategories');
Route::get('categoryAllfeatured', 'CategoryController@allfeatured');

Route::get('userAll', 'UserController@all');

//routes public home
Route::prefix('/public')
    ->group(function() {
        Route::get('/citiesByCountryCode/{country_code}', 'LocationController@getCitiesByCountryCode');
        Route::get('/categoriesByGroups/{post_type_id}', 'CategoryController@categoriesByGroups');
        Route::get('/categoriesForCarousel/{post_type_id}', [ CategoryController::class, 'categoriesForCarousel' ]);

        //Experiences
        Route::get('/experiences', 'ExperienceController@index');

        //Contract Types
        Route::get('/contractTypes', 'ContractTypeController@index');

        //Modality
        Route::get('/modalities', 'ModalityController@all');

        //PaymentTypePost
        Route::get('/paymentModalities/{post_type_id?}', [ PaymentTypePostController::class, 'all' ]);

        //Plans
        Route::get('/plans', 'PlanController@index');

        Route::prefix('/post')
            ->group(function() {
                Route::get('/urgentJobs', 'PostController@publicPostsUrgentJobs');
                Route::get('/postsByPriority/{type?}', 'PostController@postsByPriority');
                Route::get('/getPostById/{post}', 'PostController@show');
                Route::get('/filtered/{post_type_id}', 'PostController@getPostsFiltered');
                Route::post('/filterPostsByCategoryLvl2Id/{post_type_id}', [ PostController::class, 'filterPostsByCarousel' ]);
                Route::get('/all/{post_type_id?}', 'PostController@all');
            });

        Route::get('getPlacesAutocomplete/{ISOCountryCode}', [ LocationController::class, 'getPlacesAutocomplete' ]);
    });

Route::prefix('/auth')
    ->middleware(['jwt.verify'])
    ->group(function() {
        Route::post('/logout', 'AuthController@logout');
        Route::get('/category/byPlan', 'CategoryController@categoryByPlan'); //TODO falta documentar
        Route::get('/me', 'AuthController@me');
        Route::get('/verifyAuthenticationToken', 'AuthController@checkAuthToken');
        Route::post('/checkActualPassword', 'AuthController@checkActualPassword');

        Route::prefix('/category')
            ->group(function() {
                Route::post('/store', 'CategoryController@store');
                Route::post('/groupCategoryStore', 'CategoryController@groupCategoryStore');
                Route::post('/subCategoryStore', 'CategoryController@subCategoryStore');

                Route::get('/categoryShow/{category}', 'CategoryController@categoryShow');
                Route::get('/groupShow/{groupCategory}', 'CategoryController@groupShow');
                Route::get('/subCategoryShow/{subCategory}', 'CategoryController@subCategoryShow');

                Route::put('/update/{categ}', 'CategoryController@update');
                Route::put('/groupCategoryUpdate/{groupCategory}', 'CategoryController@groupCategoryUpdate');
                Route::put('/subCategoryUpdate/{subCategory}', 'CategoryController@subCategoryUpdate');

                Route::put('/destroy/{category}', 'CategoryController@destroy');
                Route::put('/groupCategoryDestroy/{groupCategory}', 'CategoryController@groupCategoryDestroy');
                Route::put('/subCategoryDestroy/{subCategory}', 'CategoryController@subCategoryDestroy');
        });

        Route::prefix('/idioms')
            ->group(function() {
                Route::get('/all', [ IdiomController::class, 'all' ] );
            });

        Route::prefix('/conditionProduct')
            ->group(function() {
                Route::get('/all', 'ConditionProductController@all');
            });

         //chatRoom
        Route::prefix('/chatRoom')
            ->group(function() {
                Route::get('/all', 'ChatRoomController@all');
                Route::post('/createChatRoom', 'PostulationController@createChatRoom');
                Route::post('/sendMessage/{chatRoom}', 'ChatRoomController@sendMessage');
                Route::get('/getChatRoomByUser/{user}', 'ChatRoomController@getChatRoomByUser');
                Route::get('/getChatRoomByReceiver/{user}', 'ChatRoomController@getChatRoomByReceiver');
                Route::put('/deleteChatRoom/{chatRoom}', 'ChatRoomController@deleteChatRoom');
                Route::get('/getMessagesByChatRoom/{chatRoom}', 'ChatRoomController@getMessagesByChatRoom');
            });

        Route::prefix('/user')
            ->group(function() {
                Route::get('/userByUserPivotCategory/{id}', 'UserController@userByUserPivotCategory');
                Route::put('/update/{user}', 'UserController@update');//? SUJETO A CAMBIOS
                Route::post('/updateRole', 'UserController@updateRole');
                Route::post('/uploadImage','UserController@uploadImage');
                Route::post('/uploadCv','UserController@uploadCv');
                Route::get('/getCV','UserController@getCV');
                Route::get('/getPaymentHistory', [UserController::class,'getPaymentsHistory']);
                Route::get('/suscription/{suscription}/invoice', [SuscriptionController::class, 'suscription']);
                Route::get('/getPosts/{post_type?}', 'PostController@getPostsFromLoggedUser');
                Route::get('/getJobsPublished', 'PostController@getJobsToPostulationsManagement');
                Route::post('/getPostsFromIdUser/{user}/{post_type?}', 'PostController@getPostsFromIdUser');
                Route::get('/show/{user}', 'UserController@show');
                Route::get('/getUserReviewsOrderingRanking/{id}', 'UserController@getUserReviewsOrderingRanking');
                //TODO no se ha documentado aun los endpoints relacionados con pagos
            });

        //post
        Route::prefix('/post')
            ->group(function() {
                Route::get('/getPostsByPriority', 'PostController@getPostsByPriority');
                // -----------------
                Route::get('/all/jobs', 'PostController@allJobs');
                Route::get('/all/{post_type_id?}', 'PostController@all');
                Route::get('/getPostById/{post}', 'PostController@show');//mandar el query params
                Route::get('/filterPostsByPriority', 'PostController@filterPostsByPriority');//filterUrgentPosts
                // -----------------

                Route::post('/store/products', 'PostController@storeProducts'); //TODO falta doc campo pictures
                Route::post('/store/services', 'PostController@storeServices');
                Route::post('/store/jobs', 'PostController@storeJobs'); //TODO falta doc campos idioms, questions.

                Route::put('/update/products/{post}', 'PostController@updateProducts'); //TODO Falta doc
                Route::put('/update/services/{post}', 'PostController@updateServices'); //TODO Falta doc
                Route::put('/update/jobs/{post}',     'PostController@updateJobs'); //TODO Falta doc

                Route::get('/showPostulationsToMyPosts/{idStage?}', 'PostulationController@showPostulationsToMyPosts');
                //post by auth

                Route::post('/filterGlobalServices', 'PostController@filterGlobalServices');
                Route::post('/filterGlobalJobs', 'PostController@filterGlobalJobs');
                Route::post('/filterGlobalProducts', 'PostController@filterGlobalProducts');

                //post favorites
                Route::get('/getFavorites/{post_type_id?}', 'PostController@getFavoritesByAuth');
                Route::post('/addToFavorites/{post_id}', 'PostController@addFavorites');
                Route::post('/deleteFavoritesMassive', 'PostController@deleteFavoritesMassive');
                Route::post('/deletePostsMassive', 'PostController@deletePostsMassive');

                Route::post('/uploadImagePost/{post}', 'PostController@uploadImagePost');
                Route::put('/deleteImage/{image}', 'PostController@deleteImage');
                Route::post('/around', 'PostController@getNearbyPosts'); //?pendiente por uso

                //JOBS
                Route::get('/filterPostsByPriorityAndType', 'PostController@filterPostsByPriorityAndType');//cambiar dominyel filterJobs
                Route::get('/urgentJobs', 'PostController@postsUrgentJobs');
                Route::get('/normalJobs', 'PostController@postsNormalJobs');
                Route::get('/show/{post}', 'PostController@show');

                Route::prefix('/jobs')
                    ->group(function() {
                        Route::prefix('/offer')
                            ->group(function() {
                                Route::get('/show/{post}/postulationsDetails', [ PostController::class, 'showJobOfferWithPostulationsDetails' ]);
                            });
                    });
            });

        //postulation
        Route::prefix('/postulations')
            ->group(function() {
                Route::get('/getByUserId/{user}', 'PostulationController@getByUserId');
                Route::get('/getJobsPostulationsByAuthUser', 'PostulationController@getJobsPostulationsByAuthUser');
                Route::get('/getById/{postulation}', 'PostulationController@getById');
                Route::put('/update/{postulation}', 'PostulationController@update');
                Route::put('/destroy', 'PostulationController@destroy');
                Route::post('/verifyPostulationUser', 'PostulationController@verifyPostulationUser');

                Route::post('/store', 'PostulationController@store');
                Route::put('/cancel/{postulation}', 'PostulationController@cancel');
                Route::put('/leaveNotes/{postulation}', 'PostulationController@leaveNotes');
                Route::put('/deleteNotes/{postulation}', 'PostulationController@deleteNotes');
                Route::put('/finalizedPostulation/{postulation}', 'PostulationController@finalizedPostulation');

                //postulation answer
                Route::resource('answers', AnswerPostulationController::class)
                    ->only([
                        'store',
                        'show',
                        'update',
                        'destroy'
                    ]);
            });


        //question postulation
        Route::prefix('/questionPublication')
            ->group(function() {
                Route::post('/store/{post}', 'QuestionPublicationController@store');
                Route::get('/getQuestionsByPostId/{post}', 'QuestionPublicationController@getQuestionsByPostId');
                Route::put('/update/{questionPost}', 'QuestionPublicationController@update');
                Route::put('/destroy/{questionPost}', 'QuestionPublicationController@destroy');
        });

        //messages
        Route::prefix('/messages')
            ->group(function() {
                Route::get('/all', 'MessageController@all');
                Route::get('/getMessagesByUser/{user}', 'MessageController@getMessagesByUser');
                Route::get('/getMessagesByChatRoom/{chatRoom}', 'MessageController@getMessagesByChatRoom');
                Route::put('/destroy/{message}', 'MessageController@destroy');
                Route::put('/readed/{message}', 'MessageController@readed');
            });

        //notifications
        Route::prefix('/notifications')
            ->group(function() {
                Route::post('/store', 'NotificationController@store');
                Route::get('/getNotificationByUser/{id}', 'NotificationController@getNotificationByUser');
                Route::put('/update/{id}', 'NotificationController@update');
                Route::put('/destroy/{id}', 'NotificationController@destroy');
                Route::put('/readed/{id}', 'NotificationController@readed');
                Route::post('/markAllAsReaded', 'NotificationController@markAllAsReaded');
            });

        //postType
        Route::prefix('/postType')
            ->group(function() {
                Route::post('/store', 'PostTypeController@store');
                Route::get('/showByIdQuestion/{id}', 'PostTypeController@showByIdQuestion');
                Route::put('/update/{id}', 'PostTypeController@update');
                Route::put('/destroy/{id}', 'PostTypeController@destroy');
                Route::get('/all', 'PostTypeController@postTypeAll');
            });

        //postStage
        //  Route::prefix('/postStage')
        //     ->group(function() {
        //         // Route::post('/store', 'postStageController@store');
        //         // Route::get('/showByIdQuestion/{id}', 'postStageController@showByIdQuestion');
        //         // Route::put('/update/{id}', 'postStageController@update');
        //         // Route::put('/destroy/{id}', 'postStageController@destroy');
        //     });

         Route::prefix('/review')
            ->group(function() {
                Route::get('/getReceivedByUserId/{user}/{ranking?}', [ReviewController::class, 'getByUserId']);
                Route::post('/store', [ReviewController::class, 'store']);
                Route::post('/answer/store', [ReviewController::class, 'storeAnswer']);
                Route::delete('/{review}', [ReviewController::class, 'destroy']);
                Route::delete('/{review}/answer', [ReviewController::class, 'destroyAnswer']);
                Route::get('/validateIfCommented', [ReviewController::class, 'validateIfCommented']);
                Route::get('/filterUserByCategory/{category_id}', [ReviewController::class, 'filterUserByCategoryId']);
            });

        //comments user (listo)
        Route::prefix('/userComment')
            ->group(function() {
                Route::post('/store',                    'UserCommentController@store');
                Route::get('/getCommentsByUserId/{user}',        'UserCommentController@getCommentsByUserId');
                Route::put( '/update/{comment}',              'UserCommentController@update');
                Route::put( '/destroy/{comment}',                  'UserCommentController@destroy');
            });

        //reportar empresa
        Route::prefix('/reports')
            ->group(function() {
                Route::post('/store/{entity}', [ ReportController::class, 'store' ]);
                Route::get('/getMotives/{entity}', [ ReportController::class, 'getMotives' ]);
                // Route::put('/update/{id}', 'ReportedUserController@update');
                // Route::put('/destroy/{id}', 'ReportedUserController@destroy');
            });
        // cancelar postulaciones
        Route::prefix('/cancelledPostulation')
            ->group(function() {
                Route::get('/getCancelationMotives', 'CancelledPostulationController@getCancelationMotives');
                Route::get('/getCancelationMotivesByPostulation/{postulation}', 'CancelledPostulationController@getCancelationMotivesByPostulation');
                Route::post('/store', 'CancelledPostulationController@store');
                Route::post('/storeMotives', 'CancelledPostulationController@store');
            });

        //motivo de report company
        // Route::prefix('/motiveUserReport')
        //     ->group(function() {
        //         Route::post('/store', 'motiveUserReportController@store');
        //         Route::get('/showByIdUser/{id}', 'motiveUserReportController@showByIdUser');
        //         Route::put('/update/{id}', 'motiveUserReportController@update');
        //         Route::put('/destroy/{id}', 'motiveUserReportController@destroy');
        //     });

        // Route::prefix('payments')->group(function() {
        //     Route::post('/paypal', [ PaypalController::class, 'pay'])->name('paypal.payment');
        //     Route::post('/paypal_subscribe', [ PaypalController::class, 'store'])->name('paypal.subscribe');

        //     Route::post('/stripe', [ StripeController::class, 'pay'])->name('stripe.payment');
        //     Route::get('/stripe/approval', [ StripeController::class, 'approval'])->name('stripe.approval');
        //     Route::get('/stripe/cancelled', [ StripeController::class, 'cancelled' ])->name('stripe.cancelled');

        //     Route::post('/stripe_subscribe', [ StripeController::class, 'store'])->name('stripe.subscribe');
        //     Route::get('/stripe_subscribe/approval', [ StripeController::class, 'subscribeApproval'])->name('stripe.subscribe.approval');
        //     Route::get('/stripe_subscribe/cancelled', [ StripeController::class, 'subscribeCancelled'])->name('stripe.subscribe.cancelled');

        //     Route::post('/subscription/call_off/recurrence', [ PlanController::class, 'callOffSubscriptionRecurrence'])->name('subscription.cancel.recurrence');
        // });

    });

Route::prefix('payments')->group(function() {
    Route::get('/paypal/approval', [ PaypalController::class, 'approval' ])->name('paypal.approval');
    Route::get('/paypal/cancelled', [ PaypalController::class, 'cancelled' ])->name('paypal.cancelled');

    Route::get('/paypal_subscribe/approval', [ PaypalController::class, 'subscribeApproval'])->name('paypal.subscribe.approval');
    Route::get('/paypal_subscribe/cancelled', [ PaypalController::class, 'suscriptionCancelled'])->name('paypal.subscribe.cancelled');

    Route::post('/paypal', [ PaypalController::class, 'pay'])->name('paypal.payment');
    Route::post('/paypal_subscribe', [ PaypalController::class, 'store'])->name('paypal.subscribe');

    Route::post('/stripe', [ StripeController::class, 'pay'])->name('stripe.payment');
    Route::get('/stripe/approval', [ StripeController::class, 'approval'])->name('stripe.approval');
    Route::get('/stripe/cancelled', [ StripeController::class, 'cancelled' ])->name('stripe.cancelled');

    Route::post('/stripe_subscribe', [ StripeController::class, 'store'])->name('stripe.subscribe');
    Route::get('/stripe_subscribe/approval', [ StripeController::class, 'subscribeApproval'])->name('stripe.subscribe.approval');
    Route::get('/stripe_subscribe/cancelled', [ StripeController::class, 'subscribeCancelled'])->name('stripe.subscribe.cancelled');

    Route::post('/subscription/call_off/recurrence', [ PlanController::class, 'callOffSubscriptionRecurrence'])->name('subscription.cancel.recurrence');
});
