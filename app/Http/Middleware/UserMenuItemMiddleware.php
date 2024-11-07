<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\UserMenuItem;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMenuItemMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {

        // if (auth()->user()->hasRole('admin_payment')) {
        //     Filament::registerUserMenuItems([
        //         'link' => UserMenuItem::make()->label('Custom Label')->url("Your Link")
        //     ]);
        // }

        // Filament::registerUserMenuItems([
        //     'account' => UserMenuItem::make()->label('My Profile')->url('/admin/my-profile')
        // ]);
        $item = [];
        $item[] = NavigationItem::make('items')
            ->icon('heroicon-o-document')
            ->url('/');

        filament()->getCurrentPanel()
            ->navigationItems($item);


        return $next($request);
    }
}
