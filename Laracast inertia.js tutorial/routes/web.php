<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use inertia\Inertia;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Auth;

Route::get('login',[LoginController::class,'create'])->name('login');
Route::post('login',[LoginController::class,'store']);
Route::post('logout',[LoginController::class,'destroy'])->middleware('auth');

Route::middleware('auth')->group(function() 
{
    Route::get('/', function () {
        return inertia::render('Home');
    });
    
    Route::get('/users', function () {
        return inertia::render('Users/Index',[
            'users'=>User::query()
            ->when(Request::input('search'), function($query,$search){
                $query->where('name','like',"%{$search}%");
            })
            ->paginate(10)
            ->withQueryString()
            ->through(fn($user) =>[
                'id'=>$user->id,
                'name'=>$user->name,
                'can' =>[
                    'edit'=>Auth::user()->can('edit',$user)
                ]
            ]),//flag
            'filters' => Request::only(['search']),
            'can'=>[
                'createUser' => Auth::user()->can('create',User::class)
            ],
            'time'=>now()->toTimeString()
        ]);
    });
    
    Route::get('/users/create', function () {
        return inertia::render('Users/Create');
    })->can('create','App\Models\User');
    
    Route::post('/users', function () {
       $attributes = Request::validate([
            'name' => 'required',
            'email' => ['required', 'email'],
            'password' => 'required',
        ]);
        // create the user
        User::create($attributes);
        // redirect
        return redirect('/users');
    });
    
    Route::get('/settings', function () {
        return inertia::render('Settings');
    });

    
});


