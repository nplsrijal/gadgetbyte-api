<?php


use App\Http\Controllers\Api\V1\UserTypeController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\PermissionController;
use App\Http\Controllers\Api\V1\UserPermissionController;
use App\Http\Controllers\Api\V1\PostCategoryController;
use App\Http\Controllers\Api\V1\ProductCategoryController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\PostPriceController;
use App\Http\Controllers\Api\V1\PostImageController;
use App\Http\Controllers\Api\V1\PostReviewController;
use App\Http\Controllers\Api\V1\AttributeController;
use App\Http\Controllers\Api\V1\PatchController;
use App\Http\Controllers\Api\V1\BrandController;
use App\Http\Controllers\Api\V1\VendorController;
use App\Http\Controllers\Api\V1\AttributeOptionController;
use App\Http\Controllers\Api\V1\VariationController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\BlindCameraTestController;
use App\Http\Controllers\Api\V1\CategoryShowcaseController;
use App\Http\Controllers\Api\V1\Frontend\PostController as FPostController;
use App\Http\Controllers\Api\V1\Frontend\ProductController as FProductController;
use App\Http\Controllers\Api\V1\Frontend\CustomerController;
use App\Http\Controllers\Api\V1\Frontend\UserBookmarkController;
use App\Http\Controllers\Api\V1\Frontend\CommentController;
use App\Http\Controllers\Api\V1\Frontend\AuthorController;
use App\Http\Controllers\Api\V1\Frontend\CategoryFilterController;
use App\Http\Controllers\Api\V1\Frontend\BlindCameraTestController as FBlindCameraTestController;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

Route::post('v1/login', [LoginController::class, 'login']);
Route::post('v1/social-login', [LoginController::class, 'socialLogin']);


Route::prefix('v1')->middleware(['apiMiddleware','verify_header'])->group(function () {
    // Route will be here

   
    Route::apiResource('usertypes', UserTypeController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('menus', MenuController::class);
    Route::apiResource('menu-permissions', PermissionController::class);
    Route::apiResource('user-menus', UserPermissionController::class);
    Route::apiResource('post-categories', PostCategoryController::class);
    Route::apiResource('tags', TagController::class);
    Route::apiResource('medias', MediaController::class);
    Route::apiResource('reviews', ReviewController::class);
    Route::apiResource('posts', PostController::class);
    Route::apiResource('post-price', PostPriceController::class);
    Route::apiResource('post-image', PostImageController::class);
    Route::apiResource('post-review', PostReviewController::class);
    Route::post('logout', [LoginController::class, 'logout']);

    Route::apiResource('product-categories', ProductCategoryController::class);
    Route::apiResource('attributes', AttributeController::class);
    Route::apiResource('brands', BrandController::class);
    Route::apiResource('vendors', VendorController::class);
    Route::post('category-attribute-options', [AttributeOptionController::class,'storeFilterAttributes']);
    Route::apiResource('attribute-options', AttributeOptionController::class);
    Route::apiResource('variations', VariationController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('blind-camera-tests', BlindCameraTestController::class);
    Route::apiResource('category-showcases', CategoryShowcaseController::class);

   

});




Route::prefix('v1/frontend')->group(function () {
    // Route will be here


    Route::apiResource('post', FPostController::class);

    
    Route::apiResource('product', FProductController::class);
    Route::apiResource('customers', CustomerController::class);
    
   
    Route::apiResource('authors', AuthorController::class);

    Route::get('patch', [PatchController::class, 'index']);
    Route::get('product_test', [PatchController::class, 'product']);
    Route::get('attribute_patch', [PatchController::class, 'patch_attributes']);
    Route::get('product-post-patch', [PatchController::class, 'patch_product_post']);

    Route::get('comments', [CommentController::class, 'index']);
    Route::get('category-side-filters', [CategoryFilterController::class, 'index']);
    Route::get('category-showcases', [CategoryFilterController::class, 'categorybar']);
    Route::apiResource('blind-camera-test', FBlindCameraTestController::class);


    Route::middleware(['apiMiddleware','verify_header'])->group(function () {
        Route::post('post/{postId}/comments', [CommentController::class, 'storePostComment']);
        Route::post('product/{productId}/comments', [CommentController::class, 'storeProductComment']);

        Route::apiResource('bookmarks', UserBookmarkController::class);
        Route::delete('comments/{id}', [CommentController::class, 'destroy']);
    
        Route::post('comments/{id}/toggle-like', [CommentController::class, 'toggleLikeDislike']);
        Route::post('comments/{id}/report', [CommentController::class, 'reportComment']);

    });

   

});
