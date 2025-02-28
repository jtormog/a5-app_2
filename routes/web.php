<?php

use App\Models\Fichero;
use App\Models\User;
use App\Models\Carpeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/ajax', function () {
    return Fichero::all();
});
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        foreach ($user->carpetasCompartidas as $carpetaCompartida) {
            app(\App\Http\Controllers\FileShareController::class)
                ->syncSharedFolders($carpetaCompartida->carpeta, $user->id);
        }
    }
    return view('welcome');
});

Route::get('/login', function(){
    return view('login');
});

Route::post('/login', function(Request $request){
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
});

Route::get('/logout', function(Request $request){
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
});

Route::post('/upload', function(Request $request){
    $fichero = new Fichero();
    $fichero->path = $request->file('uploaded_file')->store('', 'public');
    $fichero->name = $request->file('uploaded_file')->getClientOriginalName();
    $fichero->user_id = Auth::user()->id;
    $fichero->carpeta_id = $request->input('carpeta_id');
    $fichero->save();
    
    // Update folder size if file is uploaded to a folder
    if ($request->input('carpeta_id')) {
        $carpeta = Carpeta::findOrFail($request->input('carpeta_id'));
        $carpeta->updateSize();
        return redirect('/?folder=' . $request->input('carpeta_id'));
    }
    
    return redirect('/');
})->can('upload', Fichero::class);

Route::get('/download/{file}', function(Fichero $file){
    return Storage::download($file->path, $file->name);
});

// File preview route
Route::get('/preview/{file}', function(Fichero $file){
    $mimeType = Storage::disk('public')->mimeType($file->path);
    $response = [];
    
    if (str_starts_with($mimeType, 'image/')) {
        $response['type'] = 'image';
        $response['url'] = Storage::disk('public')->url($file->path);
    } else if ($mimeType === 'application/pdf') {
        $response['type'] = 'pdf';
        $response['url'] = Storage::disk('public')->url($file->path);
    } else if (str_starts_with($mimeType, 'text/')) {
        $response['type'] = 'text';
        $response['content'] = Storage::disk('public')->get($file->path);
    } else {
        $response['type'] = 'unsupported';
    }
    
    return response()->json($response);
});

// Folder deletion route
Route::get('/delete-folder/{folder}', function(Carpeta $folder){
    // Delete all files in the folder
    foreach ($folder->ficheros as $file) {
        Storage::delete($file->path);
        $file->delete();
    }
    
    // Delete all subfolders recursively
    foreach ($folder->children as $subfolder) {
        // Use a separate request to delete each subfolder
        app()->call(\Route::getRoutes()->getByName('delete-folder')->getAction()['uses'], ['folder' => $subfolder]);
    }
    
    // Delete the folder itself
    $folder->delete();
    
    return redirect('/');
})->name('delete-folder');

Route::get('/user/{user}', function(User $user){
    return view('user', compact('user'));
});

Route::get('/register', function(){
    return view('register');
});

Route::post('/register', function(Request $request){
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => $validated['password'],
    ]);

    Auth::login($user);
    return redirect('/');
});

Route::get('user/{user}/edit/', function(User $user){
    return view('edit', compact('user'));
});

Route::get('/delete/{file}', function(Fichero $file){
    Storage::delete($file->path);
    Fichero::destroy($file->id);
    return redirect('/');
})->can('delete', 'file');

// Folder routes
Route::post('/folder/create', function(Request $request){
    $carpeta = new \App\Models\Carpeta();
    $carpeta->name = $request->input('folder_name');
    $carpeta->parent_id = $request->input('parent_id');
    $carpeta->user_id = Auth::user()->id;
    $carpeta->save();
    
    if ($request->input('parent_id')) {
        return redirect('/?folder=' . $request->input('parent_id'));
    }
    
    return redirect('/');
});

Route::get('/folder/{folder?}', function($folder = null){
    return redirect('/?folder=' . $folder);
});

// File sharing routes
Route::get('/share/{type}/{id}', [\App\Http\Controllers\FileShareController::class, 'share']);
Route::get('/share-file/{file}', [\App\Http\Controllers\FileShareController::class, 'accessFile']);
Route::get('/share-folder/{folder}', [\App\Http\Controllers\FileShareController::class, 'accessFolder']);