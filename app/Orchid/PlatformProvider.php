<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Dashboard $dashboard
     *
     * @return void
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            Menu::make('Users')
                ->icon('bag')
                ->route('platform.users')
                ->title('Users Management'),
            Menu::make('Roles')
                ->icon('bag')
                ->route('platform.roles'),
            Menu::make('Address')
                ->icon('bag')
                ->route('platform.address'),

            Menu::make('Product')
                ->icon('bag')
                ->route('platform.product')
                ->title('Product Management'),
            Menu::make('Category')
                ->icon('bag')
                ->route('platform.category'),
            Menu::make('Review')
                ->icon('bag')
                ->route('platform.review'),

            Menu::make('Order')
                ->icon('bag')
                ->route('platform.order')
                ->title('Order Management'),
            Menu::make('Order Item')
                ->icon('bag')
                ->route('platform.order-item'),
        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
        ];
    }
}
