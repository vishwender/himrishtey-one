<?php

use App\Http\Controllers\Admin\AnnualIncomeController;
use App\Http\Controllers\Admin\ApiDocumentationController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\CastController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeleteProfileRequestController;
use App\Http\Controllers\Admin\EducationController;
use App\Http\Controllers\Admin\EmployerController;
use App\Http\Controllers\Admin\FamilyStatusController;
use App\Http\Controllers\Admin\HeightController;
use App\Http\Controllers\Admin\MaritalStatusController;
use App\Http\Controllers\Admin\MemberActivityController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MemberPhotoController;
use App\Http\Controllers\Admin\MemberRotationController;
use App\Http\Controllers\Admin\MembershipPlanController;
use App\Http\Controllers\Admin\MembershipTypeController;
use App\Http\Controllers\Admin\MotherTongueController;
use App\Http\Controllers\Admin\OccupationController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProfileRangeController;
use App\Http\Controllers\Admin\ReligionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\SiteStatsController;
use App\Http\Controllers\Admin\StaffActivityController;
use App\Http\Controllers\Admin\StaffUserController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\SuccessStoryController;
use App\Http\Controllers\Admin\UserRatingController;
use App\Http\Controllers\Admin\WalletOfferController;
use App\Http\Controllers\SharedProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/shared-profile/{site}/{member}', [SharedProfileController::class, 'show'])->middleware(['signed', 'throttle:60,1'])
    ->whereNumber(['site', 'member'])
    ->name('shared-profile.show');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Guest
    |--------------------------------------------------------------------------
    */

    Route::middleware('admin.guest')->group(function () {

        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    });

    /*
    |--------------------------------------------------------------------------
    | Authenticated Admin
    |--------------------------------------------------------------------------
    */

    Route::middleware('admin.auth')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Logout
        |--------------------------------------------------------------------------
        */

        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        /*
        |--------------------------------------------------------------------------
        | Site Selection
        |--------------------------------------------------------------------------
        */

        Route::get('/site/select', function () {

            return view('admin.sites.select', [
                'sites' => auth('admin')->user()
                    ->sites()
                    ->where('status', true)
                    ->get(),
            ]);
        })->name('site.select');

        Route::post('/site/{site}/switch', [SiteController::class, 'switch'])->name('site.switch');

        /*
        |--------------------------------------------------------------------------
        | Site-specific Admin
        |--------------------------------------------------------------------------
        |
        | Every route inside this group uses the selected site's
        | database connection.
        |
        */

        Route::middleware('admin.site')->group(function () {

            /*
            |--------------------------------------------------------------------------
            | Dashboard
            |--------------------------------------------------------------------------
            */

            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            Route::get('/api-documentation', [ApiDocumentationController::class, 'index'])
                ->name('api-documentation.index');

            Route::get('/site-stats', [SiteStatsController::class, 'index'])
                ->middleware('permission:view-site-stats')
                ->name('site-stats.index');

            /*
            |--------------------------------------------------------------------------
            | Members
            |--------------------------------------------------------------------------
            */

            Route::get('/members', [MemberController::class, 'index'])->name('members.index');

            /*
            |--------------------------------------------------------------------------
            | Members Advanced search
            |--------------------------------------------------------------------------
            */

            Route::get('/members/advanced-search', [MemberController::class, 'advancedSearch'])->name('members.advanced-search');

            Route::get('/members/advanced-search/results', [MemberController::class, 'advancedSearchResults'])->name('members.advanced-search.results');

            /*
            |--------------------------------------------------------------------------
            | Create Member
            |--------------------------------------------------------------------------
            */

            Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');

            Route::post('/members', [MemberController::class, 'store'])->name('members.store');

            /*
            |--------------------------------------------------------------------------
            | Show Member
            |--------------------------------------------------------------------------
            */

            Route::get('/members/{id}', [MemberController::class, 'show'])
                ->whereNumber('id')
                ->name('members.show');

            /*
            |--------------------------------------------------------------------------
            | Member Toggle Actions
            |--------------------------------------------------------------------------
            */

            Route::post('/members/{id}/toggle-status', [MemberController::class, 'toggleStatus'])->name('members.toggle-status');

            Route::post('/members/{id}/toggle-trusted', [MemberController::class, 'toggleTrusted'])->name('members.toggle-trusted');

            Route::post('/members/{id}/toggle-visibility', [MemberController::class, 'toggleVisibility'])->name('members.toggle-visibility');

            Route::post('/members/{id}/toggle-promoted', [MemberController::class, 'togglePromoted'])->name('members.toggle-promoted');

            Route::post('/members/{memberId}/rotation', [MemberController::class, 'storeRotation'])->name('members.rotation.store');

            // member delete request

            Route::post('/members/{member}/delete-request', [DeleteProfileRequestController::class, 'store'])
                ->middleware('permission:raise-profile-delete-request')
                ->name('members.delete-request');
            Route::get('/members/delete-requests', [DeleteProfileRequestController::class, 'index'])
                ->middleware('permission:view-profile-delete-requests')
                ->name('delete-profile-requests.index');

            Route::post('/members/delete-requests/{id}/accept', [DeleteProfileRequestController::class, 'accept'])
                ->middleware('permission:approve-profile-delete-request')
                ->name('delete-profile-requests.accept');

            Route::post('/members/delete-requests/{id}/reject', [DeleteProfileRequestController::class, 'reject'])
                ->middleware('permission:reject-profile-delete-request')
                ->name('delete-profile-requests.reject');

            Route::post('/members/delete-requests/bulk', [DeleteProfileRequestController::class, 'bulkAction'])
                ->middleware('permission:bulk-profile-delete-requests')
                ->name('delete-profile-requests.bulk-action');

            // =========================================================
            // Member Rotations
            // =========================================================

            // Rotation listing
            Route::get('/rotations', [MemberRotationController::class, 'index'])->name('rotations.index');

            /*
            |--------------------------------------------------------------------------
            | Member Location AJAX
            |--------------------------------------------------------------------------
            */

            Route::get('/members/location/states/{countryId}', [MemberController::class, 'getStates'])->name('members.location.states');

            Route::get('/members/location/cities/{stateId}', [MemberController::class, 'getCities'])->name('members.location.cities');

            /*
            |--------------------------------------------------------------------------
            | Member Activity
            |--------------------------------------------------------------------------
            */

            Route::get('/activities', [MemberActivityController::class, 'index'])->name('activities.index');

            Route::get('/activities/search-members', [MemberActivityController::class, 'searchMembers'])->name('activities.search-members');

            Route::get('/activities/member/{memberId}', [MemberActivityController::class, 'memberActivity'])->name('activities.member');

            Route::post('/members/{memberId}/photos/{photoId}/set-profile', [MemberController::class, 'setProfilePhoto'])->name('members.photos.set-profile');

            Route::post('/members/{memberId}/photos/{photoId}/approve', [MemberController::class, 'approvePhoto'])->name('members.photos.approve');

            Route::post('/members/{memberId}/photos/{photoId}/reject', [MemberController::class, 'rejectPhoto'])->name('members.photos.reject');

            Route::delete('/members/{memberId}/photos/{photoId}', [MemberController::class, 'deletePhoto'])->name('members.photos.delete');

            Route::get('/members/{id}/edit', [MemberController::class, 'edit'])->name('members.edit');

            Route::put('/members/{id}', [MemberController::class, 'update'])->name('members.update');

            /*
            |--------------------------------------------------------------------------
            | Member Photo Management
            |--------------------------------------------------------------------------
            */

            Route::post('/members/{memberId}/photos', [MemberPhotoController::class, 'store'])->name('members.photos.store');

            Route::post('/members/{memberId}/photos/{photoId}/set-profile', [MemberPhotoController::class, 'setProfile'])->name('members.photos.set-profile');

            Route::post('/members/{memberId}/photos/{photoId}/approve', [MemberPhotoController::class, 'approve'])->name('members.photos.approve');

            Route::post('/members/{memberId}/photos/{photoId}/unapprove', [MemberPhotoController::class, 'unapprove'])->name('members.photos.unapprove');

            Route::delete('/members/{memberId}/photos/{photoId}', [MemberPhotoController::class, 'destroy'])->name('members.photos.destroy');

            Route::post('/members/{memberId}/membership/change', [MemberController::class, 'changeMembership'])->name('members.membership.change');

            Route::post('/members/{memberId}/relationship-manager', [MemberController::class, 'updateRelationshipManager'])->name('members.relationship-manager.update');

            Route::post('/members/{memberId}/remarks', [MemberController::class, 'updateRemarks'])->name('members.remarks.update');

            Route::get('/educations', [EducationController::class, 'index'])->name('educations.index');

            Route::post('/educations', [EducationController::class, 'store'])->name('educations.store');

            Route::put('/educations/{id}', [EducationController::class, 'update'])->name('educations.update');

            Route::delete('/educations/{id}', [EducationController::class, 'destroy'])->name('educations.destroy');

            Route::get('/casts', [CastController::class, 'index'])->name('casts.index');

            Route::post('/casts', [CastController::class, 'store'])->name('casts.store');

            Route::put('/casts/{id}', [CastController::class, 'update'])->name('casts.update');

            Route::delete('/casts/{id}', [CastController::class, 'destroy'])->name('casts.destroy');

            Route::get('/religions', [ReligionController::class, 'index'])->name('religions.index');

            Route::post('/religions', [ReligionController::class, 'store'])->name('religions.store');

            Route::put('/religions/{id}', [ReligionController::class, 'update'])->name('religions.update');

            Route::delete('/religions/{id}', [ReligionController::class, 'destroy'])->name('religions.destroy');

            Route::get('/occupations', [OccupationController::class, 'index'])->name('occupations.index');

            Route::post('/occupations', [OccupationController::class, 'store'])->name('occupations.store');

            Route::put('/occupations/{id}', [OccupationController::class, 'update'])->name('occupations.update');

            Route::patch('/occupations/{id}/toggle-status', [OccupationController::class, 'toggleStatus'])->name('occupations.toggle-status');

            Route::delete('/occupations/{id}', [OccupationController::class, 'destroy'])->name('occupations.destroy');

            Route::get('/employers', [EmployerController::class, 'index'])->name('employers.index');

            Route::post('/employers', [EmployerController::class, 'store'])->name('employers.store');

            Route::put('/employers/{id}', [EmployerController::class, 'update'])->name('employers.update');

            Route::delete('/employers/{id}', [EmployerController::class, 'destroy'])->name('employers.destroy');

            Route::get('/annual-incomes', [AnnualIncomeController::class, 'index'])->name('annual-incomes.index');

            Route::post('/annual-incomes', [AnnualIncomeController::class, 'store'])->name('annual-incomes.store');

            Route::put('/annual-incomes/{id}', [AnnualIncomeController::class, 'update'])->name('annual-incomes.update');

            Route::delete('/annual-incomes/{id}', [AnnualIncomeController::class, 'destroy'])->name('annual-incomes.destroy');

            Route::get('/family-status', [FamilyStatusController::class, 'index'])->name('family-status.index');

            Route::post('/family-status', [FamilyStatusController::class, 'store'])->name('family-status.store');

            Route::put('/family-status/{id}', [FamilyStatusController::class, 'update'])->name('family-status.update');

            Route::delete('/family-status/{id}', [FamilyStatusController::class, 'destroy'])->name('family-status.destroy');

            Route::get('/marital-status', [MaritalStatusController::class, 'index'])->name('marital-status.index');

            Route::post('/marital-status', [MaritalStatusController::class, 'store'])->name('marital-status.store');

            Route::put('/marital-status/{id}', [MaritalStatusController::class, 'update'])->name('marital-status.update');

            Route::delete('/marital-status/{id}', [MaritalStatusController::class, 'destroy'])->name('marital-status.destroy');

            Route::get('/mother-tongues', [MotherTongueController::class, 'index'])->name('mother-tongues.index');

            Route::post('/mother-tongues', [MotherTongueController::class, 'store'])->name('mother-tongues.store');

            Route::put('/mother-tongues/{id}', [MotherTongueController::class, 'update'])->name('mother-tongues.update');

            Route::delete('/mother-tongues/{id}', [MotherTongueController::class, 'destroy'])->name('mother-tongues.destroy');

            Route::get('/heights', [HeightController::class, 'index'])->name('heights.index');

            Route::post('/heights', [HeightController::class, 'store'])->name('heights.store');

            Route::put('/heights/{id}', [HeightController::class, 'update'])->name('heights.update');

            Route::delete('/heights/{id}', [HeightController::class, 'destroy'])->name('heights.destroy');

            Route::get('/countries', [CountryController::class, 'index'])->name('countries.index');

            Route::post('/countries', [CountryController::class, 'store'])->name('countries.store');

            Route::put('/countries/{id}', [CountryController::class, 'update'])->name('countries.update');

            Route::delete('/countries/{id}', [CountryController::class, 'destroy'])->name('countries.destroy');

            Route::get('/states', [StateController::class, 'index'])->name('states.index');

            Route::post('/states', [StateController::class, 'store'])->name('states.store');

            Route::put('/states/{id}', [StateController::class, 'update'])->name('states.update');

            Route::delete('/states/{id}', [StateController::class, 'destroy'])->name('states.destroy');

            Route::get('/cities', [CityController::class, 'index'])->name('cities.index');

            Route::post('/cities', [CityController::class, 'store'])->name('cities.store');

            Route::put('/cities/{id}', [CityController::class, 'update'])->name('cities.update');

            Route::delete('/cities/{id}', [CityController::class, 'destroy'])->name('cities.destroy');

            // Profile Ranges

            Route::get('/profile-ranges', [ProfileRangeController::class, 'index'])->name('profile-ranges.index');

            Route::post('/profile-ranges', [ProfileRangeController::class, 'store'])->name('profile-ranges.store');

            Route::put('/profile-ranges/{id}', [ProfileRangeController::class, 'update'])->name('profile-ranges.update');

            Route::get('/membership-types', [MembershipTypeController::class, 'index'])->name('membership-types.index');

            Route::post('/membership-types', [MembershipTypeController::class, 'store'])->name('membership-types.store');

            Route::put('/membership-types/{id}', [MembershipTypeController::class, 'update'])->name('membership-types.update');

            Route::delete('/membership-types/{id}', [MembershipTypeController::class, 'destroy'])->name('membership-types.destroy');

            Route::get('/membership-plans', [MembershipPlanController::class, 'index'])->name('membership-plans.index');

            Route::post('/membership-plans', [MembershipPlanController::class, 'store'])->name('membership-plans.store');

            Route::put('/membership-plans/{id}', [MembershipPlanController::class, 'update'])->name('membership-plans.update');

            Route::delete('/membership-plans/{id}', [MembershipPlanController::class, 'destroy'])->name('membership-plans.destroy');

            Route::get('/pages', [PageController::class, 'index'])->name('pages.index');

            Route::put('/pages', [PageController::class, 'update'])->name('pages.update');

            Route::get('/user-ratings', [UserRatingController::class, 'index'])
                ->name('user-ratings.index');

            Route::get('/user-ratings/{id}', [UserRatingController::class, 'show'])
                ->name('user-ratings.show');

            Route::delete('/user-ratings/{id}', [UserRatingController::class, 'destroy'])
                ->name('user-ratings.destroy');

            Route::prefix('success-stories')->name('success-stories.')->group(function () {

                Route::get('/', [SuccessStoryController::class, 'index'])->name('index');

                Route::get('/create', [SuccessStoryController::class, 'create'])->name('create');

                Route::post('/', [SuccessStoryController::class, 'store'])->name('store');

                Route::get('/{id}/edit', [SuccessStoryController::class, 'edit'])->name('edit');

                Route::put('/{id}', [SuccessStoryController::class, 'update'])->name('update');

                Route::delete('/{id}', [SuccessStoryController::class, 'destroy'])->name('destroy');

                Route::patch('/{id}/status', [SuccessStoryController::class, 'status'])->name('status');
            }); // success stories end here

            // Payments
            Route::get('/payments', [PaymentController::class, 'index'])
                ->middleware('permission:view-payments')
                ->name('payments.index');

            // Offers
            Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');

            Route::post('/offers', [OfferController::class, 'store'])->name('offers.store');

            Route::put('/offers/{id}', [OfferController::class, 'update'])->name('offers.update');

            Route::delete('/offers/{id}', [OfferController::class, 'destroy'])->name('offers.destroy');

            Route::patch('/offers/{id}/toggle-status', [OfferController::class, 'toggleStatus'])->name('offers.toggle-status');

            // Wallet Offers
            Route::get('/wallet-offers', [WalletOfferController::class, 'index'])->name('wallet-offers.index');

            Route::post('/wallet-offers', [WalletOfferController::class, 'store'])->name('wallet-offers.store');

            Route::put('/wallet-offers/{id}', [WalletOfferController::class, 'update'])->name('wallet-offers.update');

            Route::delete('/wallet-offers/{id}', [WalletOfferController::class, 'destroy'])->name('wallet-offers.destroy');

            Route::get('/staff-activity', [StaffActivityController::class, 'index'])->name('staff-activity.index');

            Route::get('/staff/{admin}/activity', [StaffActivityController::class, 'show'])
                ->middleware('permission:view-staff-activity')
                ->name('staff.activity');

            Route::prefix('blog-posts')
                ->name('blog-posts.')
                ->group(function () {

                    // Blog post listing
                    Route::get('/', [BlogPostController::class, 'index'])->name('index');

                    // Create form
                    Route::get('/create', [BlogPostController::class, 'create'])->name('create');

                    // Store new blog post
                    Route::post('/', [BlogPostController::class, 'store'])->name('store');

                    // Edit form
                    Route::get('/{post}/edit', [BlogPostController::class, 'edit'])->name('edit');

                    // Update blog post
                    Route::put('/{post}', [BlogPostController::class, 'update'])->name('update');

                    // Publish / Unpublish
                    Route::patch('/{post}/toggle-publish', [BlogPostController::class, 'togglePublish'])->name('toggle-publish');

                    // Delete
                    Route::delete('/{post}', [BlogPostController::class, 'destroy'])->name('destroy');
                });

            Route::prefix('staff-users')
                ->name('staff-users.')
                ->group(function () {

                    Route::get('/', [StaffUserController::class, 'index'])
                        ->name('index');

                    Route::get('/create', [StaffUserController::class, 'create'])
                        ->name('create');

                    Route::post('/', [StaffUserController::class, 'store'])
                        ->name('store');

                    Route::get('/{admin}/edit', [StaffUserController::class, 'edit'])
                        ->name('edit');

                    Route::put('/{admin}', [StaffUserController::class, 'update'])
                        ->name('update');

                    Route::patch('/{admin}/toggle-status', [StaffUserController::class, 'toggleStatus'])
                        ->name('toggle-status');

                    Route::delete('/{admin}', [StaffUserController::class, 'destroy'])
                        ->name('destroy');
                }); // staff users ends here

            Route::prefix('roles')
                ->name('roles.')
                ->group(function () {

                    Route::get('/', [RoleController::class, 'index'])
                        ->name('index');

                    Route::get('/create', [RoleController::class, 'create'])
                        ->name('create');

                    Route::post('/', [RoleController::class, 'store'])
                        ->name('store');

                    Route::get('/{role}/edit', [RoleController::class, 'edit'])
                        ->name('edit');

                    Route::put('/{role}', [RoleController::class, 'update'])
                        ->name('update');

                    Route::delete('/{role}', [RoleController::class, 'destroy'])
                        ->name('destroy');
                }); // roles end here

            Route::prefix('permissions')
                ->name('permissions.')
                ->group(function () {

                    Route::get('/', [PermissionController::class, 'index'])
                        ->name('index');

                    Route::post('/', [PermissionController::class, 'store'])
                        ->name('store');

                    Route::put('/{permission}', [PermissionController::class, 'update'])
                        ->name('update');

                    Route::delete('/{permission}', [PermissionController::class, 'destroy'])
                        ->name('destroy');
                }); // permissions end here
        });
    });
});
