<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/* import db facade */
use Illuminate\Support\Facades\DB;
use App\Models\Favorites;
use App\Models\User;

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

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */


// Route::post('/api/send', function (Request $request) {
//   $contact = new Contact;
//   $contact->name = $request->name;
//   $contact->email = $request->email;
//   $contact->message = $request->message;
//   $contact->save();

//   // Send email
//   Mail::to('springrose007@hotmail.com')->send(new ContactMail($contact));

//   return response()->json(['message' => 'Email sent successfully']);
// });


/* routes and endpoints for favorites */

Route::get('/favorites', function () {
    $favorites = DB::select('SELECT * FROM favorites');
    return response()->json($favorites);
  });
  
  Route::post('/favorites', function (Request $request) {
    $user_id = $request->user_id;
    $recipe_id = $request->recipe_id;

    // Check if recipe is already in favorites
    $favorite = DB::select('SELECT * FROM favorites WHERE user_id = ? AND recipe_id = ?', [$user_id, $recipe_id]);
    if ($favorite) {
      return response()->json(['message' => 'Recipe already in favorites'], 409);
    }
  
    DB::insert('INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)', [$user_id, $recipe_id]);
    return response()->json(['message' => 'Favorite added to list'], 201);
  });
  
  Route::get('/favorites/{id}', function ($id) {
    $favorites = DB::select('SELECT * FROM favorites WHERE id = ?', [$id]);
    return response()->json($favorites);
  });
  
  Route::post('/favorites/{id}', function (Request $request, $id) {
    $user_id = $request->user_id;
    $recipe_id = $request->recipe_id;
  });

    // To delete a favorite
  Route::post('/favorites/delete/{id}', function ($id) {
    DB::delete('DELETE FROM favorites WHERE id = ?', [$id]);
    return response()->json(['message' => 'favorite deleted successfully'], 204);
  });



// Routes and endpoints for recipes

Route::post('/recipes', function (Request $request) {
  $title = $request->title;
  $ingredient= $request->ingredient;
  $step = $request->step;
  $user_id = $request->user_id;
  // Add other fields as necessary

  DB::insert('INSERT INTO recipes (title, ingredient, step, user_id) VALUES (?, ?, ?, ?)', [$title, $ingredient, $step, $user_id]);
  return response()->json(['message' => 'Recipe added'], 201);
});

Route::get('/recipes', function () {
  $recipes = DB::table('recipes')->get();
  return response()->json($recipes);
});

// Delete recipe
Route::post('/recipes/delete/{id}', function ($id) {
  DB::delete('DELETE FROM recipes WHERE id = ?', [$id]);
  return response()->json(['message' => 'Recipe deleted successfully'], 204);
});

 // Routes and endpoints for users

 Route::get('/users', function () {
    $users = DB::table('users')->get();
    return response()->json($users);
  });
 
  Route::post('/users', function (Request $request) {
    // return $request->all();

   $validatedData = $request->validate([
       'firstname' => 'required|max:255',
       'lastname' => 'required|max:255',
       'email' => 'required|email|unique:users',
       'password' => 'required',
       'username' => 'required|max:255',
   ]);

   // dd($validatedData['firstname']);
 
   $user = User::create([
       'firstname' => $validatedData['firstname'],
       'lastname' => $validatedData['lastname'],
       'username' => $validatedData['username'],
       'email' => $validatedData['email'],
       'password' => bcrypt($validatedData['password']),

   ]);

   // Check if username already exists
   if (User::where('username', $validatedData['username'])->exists()) {
       return response()->json(['message' => 'Username already exists'], 409);
   }

   // Check if email already exists
   if (User::where('email', $validatedData['email'])->exists()) {
       return response()->json(['message' => 'Email already exists'], 409);
   }
 
   // Ensure the User model is using the HasApiTokens trait
   $token = $user->createToken('auth_token')->plainTextToken;
 
   // Update the remember_token in the database with the new token
   DB::table('users')
       ->where('id', $user->id)
       ->update(['remember_token' => $token]);
 
   return response()->json(['id' => $user->id, 'token' => $token], 201);
 });

 
  // temp token routes
  Route::post('/tokens/create', function (Request $request) {
     $user = User::find(1);
     //return $user;
     $token = $user->createToken('mynewtoken');
     return ['token' => $token->plainTextToken];
 
     // post the plaintexttoken to the user in the database
     $user:: where('id', 1)->update(['remember_token' => $token->plainTextToken]);
  });