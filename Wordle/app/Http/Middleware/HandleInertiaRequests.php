<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;


class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

  
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth'=>Auth::user() ? [
                'user' =>[
                    'username' => Auth::user()->name
                ] // safer way no 1 on 1 relation
            ] : null
        ]);
    }
}
