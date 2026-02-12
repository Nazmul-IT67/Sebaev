<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\MiniGoalViewController;
use App\Http\Controllers\Api\MovementViewController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\OnBodingController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\SocialLinkController;
use App\Http\Controllers\Api\DynamicPageController;
use App\Http\Controllers\Api\SitesettingController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\ConnectAccount;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\DonationHistoryController;
use App\Http\Controllers\Api\FirebaseTokenController;
use App\Http\Controllers\Api\Web\DashboardController;
use App\Http\Controllers\Api\JoinMovementController;
use App\Http\Controllers\Api\MovementController;
use App\Http\Controllers\Api\MovementResponseVideoCommentController;
use App\Http\Controllers\Api\NewsFeedMovementController;
use App\Http\Controllers\Api\OnBodingMovementController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PostResponseVideoCommentController;
use App\Http\Controllers\Api\ReportMovementController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SingleChatController;
use App\Http\Controllers\Api\SuggestCategoryMovementController;
use App\Http\Controllers\Api\VideoCommentController;
use App\Http\Controllers\Api\Web\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


//Social Login
Route::post('/social-login', [SocialAuthController::class, 'socialLogin']);

//Register API
Route::controller(RegisterController::class)->prefix('users/register')->group(function () {
    // User Register
    Route::post('/', 'userRegister');

    // Verify OTP
    Route::post('/otp-verify', 'otpVerify');

    // Resend OTP
    Route::post('/otp-resend', 'otpResend');
});

//Login API
Route::controller(LoginController::class)->prefix('users/login')->group(function () {

    // User Login
    Route::post('/', 'userLogin');

    // Verify Email
    Route::post('/email-verify', 'emailVerify');

    // Resend OTP
    Route::post('/otp-resend', 'otpResend');

    // Verify OTP
    Route::post('/otp-verify', 'otpVerify');

    //Reset Password
    Route::post('/reset-password', 'resetPassword');
});

Route::controller(SitesettingController::class)->group(function () {
    Route::get('/site-settings', 'siteSettings');
});

//Dynamic Page
Route::controller(DynamicPageController::class)->group(function () {
    Route::get('/dynamic-pages', 'dynamicPages');
    Route::get('/dynamic-pages/single/{slug}', 'single');
});

//Social Links
Route::controller(SocialLinkController::class)->group(function () {
    Route::get('/social-links', 'socialLinks');
});

//FAQ APIs
Route::controller(FaqController::class)->group(function () {
    Route::get('/faq/all', 'FaqAll');
});

// Country Controller API
Route::get('/country',  CountryController::class);

// Home Page Banner API
Route::get('/banner/page', HomeController::class);

// Search API
Route::get('/search',  SearchController::class);

// Home Page Movements API
Route::group(['middleware' => ['guest']], function () {
    Route::get('/movements',  NewsFeedMovementController::class);

    Route::controller(MovementController::class)->group(function () {
        Route::get('/single-movement/{id}', 'singleMovement');
    });

    Route::controller(PostController::class)->group(function () {
        Route::get('/single-post/{id}', 'singlePost');
    });

    Route::controller(SubCategoryController::class)->group(function () {
        Route::get('/subcategory', 'subcategory');
    });
});

// Guest Single Movement APIs
// Route::get('/guest/single-movement/{id}', 'guestSingleMovement');

Route::group(['middleware' => ['jwt.verify', 'user.active.status']], function () {

    // User Profile
    Route::post('/on-boding', OnBodingController::class);

    //All OnBoding Movements
    Route::get('/all-movement', OnBodingMovementController::class);

    // Join & Leave Movement
    Route::controller(JoinMovementController::class)->group(function () {
        Route::post('/join-movement/{id}', 'joinMovement');
        Route::post('/leave-movement/{id}',  'leaveMovement');
        Route::post('/join-movement',  'joinMultipleMovement');
    });

    // User APIs
    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/data', 'userData');
        Route::post('/data/update', 'userUpdate');
        Route::post('/password/change', 'passwordChange');
        Route::post('/logout', 'logoutUser');
        Route::delete('/delete', 'deleteUser');
        Route::post('/update-language', 'updateLanguage');
    });

    // Movements APIs
    Route::controller(MovementController::class)->group(function () {
        Route::post('/create-movement',  'store');
        Route::post('/update-movement/{id}', 'update');
        Route::post('/delete-movement/{id}', 'delete');
        Route::get('/my-movement', 'myMovement');
        Route::get('/my-join-movement',  'myJoinMovement');
        Route::get('/suggest-movement', 'suggestMovement');
    });

    // Report Movement API
    Route::post('/report-post',  ReportMovementController::class);

    // Home Page Suggest Movement API
    Route::get('/suggest/movement',  SuggestCategoryMovementController::class);

    Route::controller(PostController::class)->group(function () {
        Route::post('/create-post/{id}', 'create');
        Route::post('/edit-post/{id}', 'EditPost');
        Route::post('/delete-post/{id}', 'DeletePost');
        Route::get('/all-post/{id}', 'allPost');
    });

    Route::controller(MovementViewController::class)->group(function () {
        Route::post('/movements/{movementId}/view', 'store');
    });

    Route::controller(MiniGoalViewController::class)->group(function () {
        Route::post('/mini-goals/{postId}/view', 'store');
    });

    Route::controller(NotificationController::class)->group(function () {
        Route::get('/notification', 'notification');
    });

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/engagement', 'Engagement');
    });

    Route::controller(VideoCommentController::class)->group(function () {
        Route::post('/create/video-comment/{id}', 'store');
        Route::get('/video-comment/{id}', 'allVideoComment');
        Route::delete('/delete/video-comment/{id}', 'deleteVideoComment');
    });

    Route::controller(PaymentController::class)->group(function () {
        Route::post('/donate', 'donate');
    });

    Route::controller(ConnectAccount::class)->prefix('stripe/account')->group(function () {
        Route::post('/connect', 'connectAccount');
    });

    Route::controller(DonationHistoryController::class)->group(function () {
        Route::get('/donation/history', 'donationHistory');
    });

    Route::controller(SingleChatController::class)->group(function () {
        Route::get('/active-users', 'activeUsers');
        Route::get('/chats', 'chats');
        Route::get('/group/chats', 'groupChats');
        Route::get('/chat', 'chat');
        Route::post('/chat/send', 'sendMessage');
    });

    Route::controller(FirebaseTokenController::class)->group(function () {
        Route::post('/firebase-token/add', 'updateFirebaseToken');
        Route::post('/firebase-token/delete', 'deleteFirebaseToken');
        Route::get('/firebase-token','FirebaseToken');
    });

    Route::controller(PostResponseVideoCommentController::class)->group(function () {
        // Get all comments for a video
        Route::get('videos/{video}/comments', 'index');

        // Post a new comment on a video
        Route::post('videos/{video}/comments', 'store');

        // Reply to an existing comment
        Route::post('comments/{comment}/replies', 'reply');

        // Delete a comment/reply
        Route::delete('comments/{comment}', 'destroy');

        // Edit a comment/reply
        Route::post('comments/{comment}/update', 'update');
    });

    Route::controller(MovementResponseVideoCommentController::class)->group(function () {
        // Get all comments for a video
        Route::get('movement-response-videos/{video}/comments', 'index');

        // Post a new comment on a video
        Route::post('movement-response-videos/{video}/comments', 'store');

        // Reply to an existing comment
        Route::post('movement-video-comments/{comment}/replies', 'reply');

        // Update a comment
        Route::post('video-comments/{comment}/update', 'update');

        // Delete a comment
        Route::delete('video-comments/{comment}', 'destroy');
    });
});

Route::controller(PaymentController::class)->group(function () {
    Route::get('/checkout-success', 'checkoutSuccess')->name('checkout.success');
    Route::get('/checkout-cancel', 'checkoutCancel')->name('checkout.cancel');
});

Route::controller(ConnectAccount::class)->prefix('instructor')->group(function () {
    Route::get('/connect/success', 'success')->name('connect.success');
    Route::get('/connect/cancel', 'cancel')->name('connect.cancel');
});

 // Route::controller(CommentController::class)->group(function () {
    //     Route::post('/create/comment/{id}', 'store');
    //     Route::post('/create/reply/{id}', 'commentReplyStore');
    //     Route::get('/comment/reply/{id}',  'allCommentReply');
    //     Route::post('/edit/comment/{id}', 'editComment');
    //     Route::post('/delete/comment/{id}',  'deleteComment');
    //     Route::post('/edit/reply/{id}',  'editReply');
    //     Route::post('/delete/reply/{id}', 'deleteReply');
    // });
