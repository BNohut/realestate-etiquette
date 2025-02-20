<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * @param Dashboard $dashboard
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * @return Menu[]
     */
    public function registerMainMenu(): array
    {
        return [
            Menu::make(__('HOME'))
                ->icon('house')
                ->route('platform.main'),

            Menu::make(__('OFFICES'))
                ->icon('buildings')
                ->route('platform.office')->permission('platform.offices'),

            Menu::make(__('CONTACTS'))
                ->icon('people')
                ->route('platform.contact')->permission('platform.contacts'),

            Menu::make(mb_strtoupper(__('Consultants')))
                ->icon('person-vcard')
                ->route('platform.consultant')->permission('platform.consultants'),

            Menu::make(__('PORTFOLIOS'))
                ->icon('book')
                ->route('platform.portfolio')->permission('platform.portfolios'),

            Menu::make(__('ADD CALL RECORD'))
                ->icon('telephone')
                ->route('platform.call.edit')->permission('platform.calls.add'),

            Menu::make(__('RECORDS'))
                ->icon('journal-bookmark-fill')
                ->list([
                    Menu::make(__('All Records'))->icon('file-earmark-text')->route('platform.record'),
                    Menu::make(__('Calls'))->icon('headset')->route('platform.call'),
                    Menu::make(__('FSBOs'))->icon('diamond')->route('platform.fsbo'),
                    Menu::make(__('Viewings'))->icon('flag')->route('platform.viewing'),
                    Menu::make(__('Customers'))->icon('list')->route('platform.customer'),
                    Menu::make(__('Marketing'))->icon('magic')->route('platform.marketing'),
                    Menu::make(__('Sale Closing'))->icon('rocket')->route('platform.sale'),
                    Menu::make(__('Deed Sale-Rent Processes'))->icon('file-earmark-medical')->route('platform.deed'),
                ])->permission('platform.records'),

            Menu::make(__('SYSTEM'))
                ->icon('gear')
                ->list([
                    Menu::make(__('Users'))
                        ->icon('person')
                        ->title(__('Admin'))
                        ->route('platform.systems.users')
                        ->permission('platform.systems.users'),
                    Menu::make(__('Roles'))
                        ->icon('lock')
                        ->route('platform.systems.roles')
                        ->permission('platform.systems.roles'),
                    Menu::make(__('Provinces'))
                        ->icon('geo-alt')
                        ->title(__('Super Admin'))
                        ->route('platform.province')
                        ->permission('platform.systems.provinces'),
                    Menu::make(__('States'))
                        ->icon('geo')
                        ->route('platform.state')
                        ->permission('platform.systems.states'),
                    Menu::make(__('Record Types'))
                        ->icon('journal-plus')
                        ->route('platform.recordtypes')
                        ->permission('platform.recordtypes'),
                    Menu::make(__('Configuration'))
                        ->icon('gear')
                        ->route('platform.config')
                        ->permission('platform.systems.settings')
                ])->permission('platform.recordtypes'),
        ];
    }

    /**
     * @return Menu[]
     */
    public function registerProfileMenu(): array
    {
        return [

            Menu::make(__('MY OFFICE'))
                ->icon('buildings')
                ->permission('platform.myoffice')
                ->list([
                    Menu::make(__('Consultant Management'))
                        ->icon('person')
                        ->route('platform.office.consultant'),
                    Menu::make(__('Assistant Management'))
                        ->icon('person-workspace')
                        ->route('platform.office.assistant'),
                    Menu::make(__('Deed Sale-Rent Management'))
                        ->icon('file-earmark-medical')->route('platform.office.deed'),
                ])->title(__('User')),

            Menu::make(mb_strtoupper(__('Profile')))
                ->route(
                    authUserInRole('ofis-asistani') ? 'platform.office.assistant.edit' : 'platform.profile',
                    authUserInRole('ofis-asistani') ? ['user' => auth()->user()->id] : null
                )
                ->icon('person'),

            Menu::make(__('LOGOUT'))
                ->icon('box-arrow-left')
                ->route('user.logout')
        ];
    }

    /**
     * @return ItemPermission[]
     */
    public function registerPermissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('List Roles'))
                ->addPermission('platform.systems.roles.add', __('Add Role'))
                ->addPermission('platform.systems.roles.edit', __('Edit Role'))
                ->addPermission('platform.systems.roles.remove', __('Remove Role'))
                ->addPermission('platform.systems.users', __('List Users'))
                ->addPermission('platform.systems.users.add', __('Add User'))
                ->addPermission('platform.systems.users.edit', __('Edit User'))
                ->addPermission('platform.systems.users.remove', __('Remove User'))
                ->addPermission('platform.systems.settings', __('Configuration'))
                ->addPermission('platform.systems.provinces', __('Province Management'))
                ->addPermission('platform.systems.states', __('State Management')),
            ItemPermission::group(__('Office Management'))
                ->addPermission('platform.offices', __('List Offices'))
                ->addPermission('platform.offices.add', __('Add Office'))
                ->addPermission('platform.offices.edit', __('Edit Office'))
                ->addPermission('platform.offices.remove', __('Remove Office')),
            ItemPermission::group(__('My Office'))
                ->addPermission('platform.myoffice', __('My Office')),
            ItemPermission::group(__('Call Records'))
                ->addPermission('platform.calls.add', __('Add Call Record')),
            ItemPermission::group(__('Record Management'))
                ->addPermission('platform.records', __('List Records'))
                ->addPermission('platform.records.add', __('Add Record'))
                ->addPermission('platform.records.edit', __('Edit Record'))
                ->addPermission('platform.records.remove', __('Remove Record'))
                ->addPermission('platform.records.detail', __('Record Detail')),
            ItemPermission::group(__('Record Type Management'))
                ->addPermission('platform.recordtypes', __('List Record Types'))
                ->addPermission('platform.recordtypes.add', __('Add Record Type'))
                ->addPermission('platform.recordtypes.edit', __('Edit Record Type'))
                ->addPermission('platform.recordtypes.remove', __('Remove Record Type')),
            ItemPermission::group(__('Contact Management'))
                ->addPermission('platform.contacts', __('List Contacts'))
                ->addPermission('platform.contacts.add', __('Add Contact'))
                ->addPermission('platform.contacts.edit', __('Edit Contact'))
                ->addPermission('platform.contacts.remove', __('Remove Contact'))
                ->addPermission('platform.contacts.detail', __('Contact Detail')),
            ItemPermission::group(__('Consultant Management'))
                ->addPermission('platform.consultants', __('List Consultants'))
                ->addPermission('platform.consultants.add', __('Add Consultant'))
                ->addPermission('platform.consultants.edit', __('Edit Consultant'))
                ->addPermission('platform.consultants.remove', __('Remove Consultant'))
                ->addPermission('platform.consultants.detail', __('Consultant Detail')),
            ItemPermission::group(__('Portfolio Management'))
                ->addPermission('platform.portfolios', __('List Portfolios'))
                ->addPermission('platform.portfolios.add', __('Add Portfolio'))
                ->addPermission('platform.portfolios.edit', __('Edit Portfolio'))
                ->addPermission('platform.portfolios.remove', __('Remove Portfolio'))
                ->addPermission('platform.portfolios.detail', __('Portfolio Detail'))

        ];
    }
}
