<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/* import db facade */
use Illuminate\Support\Facades\DB;
use App\Models\Favorites;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

route::get('/hello', function () {
    return DB::table('favorites')->get();
});

route::get('/login', function () {
    return 'Acces denied, please login';
})->name('login');

/* routes and endpoints for favorites */

Route::middleware('auth:sanctum')->get('/favorites', function () {
    $favorites = DB::select('SELECT * FROM favorites');
    return response()->json($favorites);
  });
  
  Route::post('/favorites', function (Request $request) {
    $user_id = $request->user_id;
    $recipe_id = $request->recipe_id;
    // Add other fields as necessary
  
    DB::insert('INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)', [$user_id, $recipe_id]);
    return response()->json(['message' => 'Favorite added to list'], 201);
  });
  
  Route::get('/favorites/{id}', function ($id) {
    $favorites = DB::select('SELECT * FROM favorites WHERE id = ?', [$id]);
    return response()->json($favorites);
  });
  
  Route::put('/favorites/{id}', function (Request $request, $id) {
    $user_id = $request->user_id;
    $recipe_id = $request->recipe_id;
    // Add other fields as necessary
  
    DB::update('UPDATE favorites SET user_id = ?, recipe_id = ? WHERE id = ?', [$user_id, $recipe_id, $id]);
  
    return response()->json(['message' => 'favorites updated successfully']);
  });
  
  Route::delete('/favorites/{id}', function ($id) {
    DB::delete('DELETE FROM favorites WHERE id = ?', [$id]);
    return response()->json(['message' => 'favorite deleted successfully'], 204);
  });
  
  
 
  // Routes and endpoints for users
 
 
 
  // temp token routes
  Route::post('/tokens/create', function (Request $request) {
     $user = User::find(1);
     //return $user;
     $token = $user->createToken('mynewtoken');
     return ['token' => $token->plainTextToken];
 
     // post the plaintexttoken to the user in the database
     $user:: where('id', 1)->update(['remember_token' => $token->plainTextToken]);
  });
