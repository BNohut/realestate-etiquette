<?php

declare(strict_types=1);

use App\Http\Controllers\AjaxController;
use App\Http\Middleware\AssistantEditPermissionMiddleware;
use App\Http\Middleware\ConsultantDetailPermissionMiddleware;
use App\Http\Middleware\ConsultantEditDetailPermissionMiddleware;
use App\Http\Middleware\ContactEditDetailPermissionMiddleware;
use App\Http\Middleware\PortfolioEditPermissionMiddleware;
use App\Http\Middleware\UserPermissionCheckMiddleware;
use App\Orchid\Screens\Call\CallDetailScreen;
use App\Orchid\Screens\Call\CallEditScreen;
use App\Orchid\Screens\Call\CallScreen;
use App\Orchid\Screens\Config\ConfigScreen;
use App\Orchid\Screens\Consultant\ConsultantDetailScreen;
use App\Orchid\Screens\Consultant\ConsultantEditScreen;
use App\Orchid\Screens\Consultant\ConsultantScreen;
use App\Orchid\Screens\Contact\ContactDetailScreen;
use App\Orchid\Screens\Contact\ContactEditScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;
use App\Orchid\Screens\Province\ProvinceScreen;
use App\Orchid\Screens\Province\ProvinceEditScreen;
use App\Orchid\Screens\State\StateScreen;
use App\Orchid\Screens\State\StateEditScreen;
use App\Orchid\Screens\Contact\ContactScreen;
use App\Orchid\Screens\Customer\CustomerDetailScreen;
use App\Orchid\Screens\Customer\CustomerEditScreen;
use App\Orchid\Screens\Customer\CustomerScreen;
use App\Orchid\Screens\Deed\DeedDetailScreen;
use App\Orchid\Screens\Deed\DeedEditScreen;
use App\Orchid\Screens\Deed\DeedManagementScreen;
use App\Orchid\Screens\Deed\DeedScreen;
use App\Orchid\Screens\FSBO\FSBODetailScreen;
use App\Orchid\Screens\FSBO\FSBOEditScreen;
use App\Orchid\Screens\FSBO\FSBOScreen;
use App\Orchid\Screens\Marketing\MarketingDetailScreen;
use App\Orchid\Screens\Marketing\MarketingEditScreen;
use App\Orchid\Screens\Marketing\MarketingScreen;
use App\Orchid\Screens\Office\AssistantEditScreen;
use App\Orchid\Screens\Office\AssistantScreen;
use App\Orchid\Screens\Office\OfficeEditScreen;
use App\Orchid\Screens\Office\OfficeConsultantScreen;
use App\Orchid\Screens\Office\OfficeScreen;
use App\Orchid\Screens\Portfolio\PortfolioDetailScreen;
use App\Orchid\Screens\Portfolio\PortfolioEditScreen;
use App\Orchid\Screens\Portfolio\PortfolioScreen;
use App\Orchid\Screens\Record\RecordScreen;
use App\Orchid\Screens\RecordType\RecordTypeEditScreen;
use App\Orchid\Screens\RecordType\RecordTypeScreen;
use App\Orchid\Screens\Sale\SaleDetailScreen;
use App\Orchid\Screens\Sale\SaleEditScreen;
use App\Orchid\Screens\Sale\SaleScreen;
use App\Orchid\Screens\Viewing\ViewingDetailScreen;
use App\Orchid\Screens\Viewing\ViewingEditScreen;
use App\Orchid\Screens\Viewing\ViewingScreen;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

//ETIQUETTE ROUTES
// İller
Route::screen('/province', ProvinceScreen::class)->name('platform.province')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Provinces'), route('platform.province')));
Route::screen('/province/{province?}', ProvinceEditScreen::class)->name('platform.province.edit')
    ->breadcrumbs(fn (Trail $trail, $province) => $trail
        ->parent('platform.province')
        ->push($province->name, route('platform.province.edit', $province)));
// İlçeler
Route::screen('/state', StateScreen::class)->name('platform.state')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.province')
        ->push(__('States'), route('platform.state')));
Route::screen('/state/{state?}', StateEditScreen::class)->name('platform.state.edit')
    ->breadcrumbs(fn (Trail $trail, $state) => $trail
        ->parent('platform.province.edit', $state->province)
        ->push($state->name, route('platform.state.edit', $state)));
// Offices (Ofisler)
Route::screen('/offices', OfficeScreen::class)->name('platform.office')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Offices'), route('platform.office')));
Route::screen('/office-consultants', OfficeConsultantScreen::class)->name('platform.office.consultant')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Consultant Management'), route('platform.office.consultant')));
Route::screen('/assistant', AssistantScreen::class)->name('platform.office.assistant')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Assistant Management'), route('platform.office.assistant')));
Route::screen('/assistant/{user?}', AssistantEditScreen::class)->name('platform.office.assistant.edit')
    ->middleware(AssistantEditPermissionMiddleware::class)
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.office.assistant')
        ->push($user->name . ' ' . $user->last_name, route('platform.office.assistant.edit', $user->id)));
Route::screen('/office/create', OfficeEditScreen::class)->name('platform.office.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.office')
        ->push(__('Create'), route('platform.office.create')));
Route::screen('/office/{office?}', OfficeEditScreen::class)->name('platform.office.edit')
    ->breadcrumbs(fn (Trail $trail, $office) => $trail
        ->parent('platform.office')
        ->push($office->name, route('platform.office.edit', $office)));
// Contacts (Kişiler)
Route::screen('/contacts', ContactScreen::class)->name('platform.contact')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Contacts'), route('platform.contact')));

Route::screen('/contact/{contact?}', ContactEditScreen::class)->name('platform.contact.edit')
    ->middleware(ContactEditDetailPermissionMiddleware::class)
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.contact')
        ->push(__('Contacts'), route('platform.contact.edit')));

Route::screen('/contact/{contact?}/detail', ContactDetailScreen::class)->name('platform.contact.detail')
    ->middleware(ContactEditDetailPermissionMiddleware::class)
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.contact')
        ->push(__('Contact Information'), route('platform.contact.detail')));
// Record Types (Kayıt Türleri)
Route::screen('/record-types/{recordtype:id?}', RecordTypeScreen::class)->name('platform.recordtypes')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Record Types'), route('platform.recordtypes')));

Route::screen('/record-type/{recordtype?}', RecordTypeEditScreen::class)->name('platform.recordtype.edit')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.recordtypes')
        ->push(__('Record Type'), route('platform.recordtype.edit')));

// Consultants (Danışmanlar)
Route::screen('/consultants', ConsultantScreen::class)->name('platform.consultant')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Consultants'), route('platform.consultant')));

Route::screen('/consultant/{consultant?}', ConsultantEditScreen::class)->name('platform.consultant.edit')
    ->middleware(ConsultantEditDetailPermissionMiddleware::class)
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.consultant')
        ->push(__('Consultant'), route('platform.consultant.edit')));

Route::screen('/consultant/{consultant?}/detail', ConsultantDetailScreen::class)->name('platform.consultant.detail')
    ->middleware(ConsultantDetailPermissionMiddleware::class)
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.consultant')
        ->push(__('Consultant Information'), route('platform.consultant.detail')));

// Portfolios (Portföyler)
Route::screen('/portfolios', PortfolioScreen::class)->name('platform.portfolio')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Portfolios'), route('platform.portfolio')));

Route::screen('/portfolio/{portfolio?}', PortfolioEditScreen::class)->name('platform.portfolio.edit')
    ->middleware(PortfolioEditPermissionMiddleware::class)
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.portfolio')
        ->push(__('Portfolio'), route('platform.portfolio.edit')));

Route::screen('/portfolio/{portfolio?}/detail', PortfolioDetailScreen::class)->name('platform.portfolio.detail')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.portfolio')
        ->push(__('Portfolio Detail'), route('platform.portfolio.detail')));

// Config (Ayarlar)
Route::screen('/config', ConfigScreen::class)->name('platform.config')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Configuration'), route('platform.config')));

// Ajax Routes
Route::get('/ajax/delete-image', [AjaxController::class, 'deleteImage']);
Route::get('/ajax/delete-portfolio', [AjaxController::class, 'deletePortfolio']);
Route::get('/ajax/delete-record', [AjaxController::class, 'deleteRecord']);

// Record Route
Route::screen('/records', RecordScreen::class)->name('platform.record')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Records'), route('platform.record')));
// Call Record Routes
Route::screen('/calls/{portfolio?}', CallScreen::class)->name('platform.call')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Calls'), route('platform.call')));

Route::screen('/call/{record?}/detail', CallDetailScreen::class)->name('platform.call.detail')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.call')
        ->push(__('Call Detail'), route('platform.call.detail')));

// FSBO Record Routes
Route::screen('/fsboes', FSBOScreen::class)->name('platform.fsbo')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('FSBO'), route('platform.fsbo')));

Route::screen('/fsbo/{record?}/detail', FSBODetailScreen::class)->name('platform.fsbo.detail')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.fsbo')
        ->push(__('FSBO Detail'), route('platform.fsbo.detail')));

// Viewing Record Routes
Route::screen('/viewings/{portfolio?}', ViewingScreen::class)->name('platform.viewing')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Viewing'), route('platform.viewing')));

Route::screen('/viewing/{record?}/detail', ViewingDetailScreen::class)->name('platform.viewing.detail')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.viewing')
        ->push(__('Viewing Detail'), route('platform.viewing.detail')));

// Customer Record Routes
Route::screen('/customers', CustomerScreen::class)->name('platform.customer')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Customer'), route('platform.customer')));

Route::screen('/customer/{record?}/detail', CustomerDetailScreen::class)->name('platform.customer.detail')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.customer')
        ->push(__('Customer Detail'), route('platform.customer.detail')));

// Marketing Record Routes
Route::screen('/marketings/{portfolio?}', MarketingScreen::class)->name('platform.marketing')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Marketing'), route('platform.marketing')));

Route::screen('/marketing/{record?}/detail', MarketingDetailScreen::class)->name('platform.marketing.detail')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.marketing')
        ->push(__('Marketing Detail'), route('platform.marketing.detail')));

// Deed Process Record Routes
Route::screen('/deeds', DeedScreen::class)->name('platform.deed')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Deed Sale-Rent Processes'), route('platform.deed')));

Route::screen('/deed/{record?}/detail', DeedDetailScreen::class)->name('platform.deed.detail')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.deed')
        ->push(__('Deed Process Detail'), route('platform.deed.detail')));

// Sale Closing Record Routes
Route::screen('/sales', SaleScreen::class)->name('platform.sale')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Sale Closing'), route('platform.sale')));

Route::screen('/sale/{record?}/detail', SaleDetailScreen::class)->name('platform.sale.detail')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.sale')
        ->push(__('Sale Closing Detail'), route('platform.sale.detail')));

// Deed Sale-Rent Process Management
Route::screen('/deed-management', DeedManagementScreen::class)->name('platform.office.deed')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Deed Sale-Rent Management'), route('platform.office.deed')));

Route::middleware(UserPermissionCheckMiddleware::class)->group(function () {
    Route::screen('/call/{record?}', CallEditScreen::class)
        ->name('platform.call.edit')
        ->breadcrumbs(fn (Trail $trail) => $trail
            ->parent('platform.call')
            ->push(__('Edit'), route('platform.call.edit')));

    Route::screen('/fsbo/{record?}', FSBOEditScreen::class)->name('platform.fsbo.edit')
        ->breadcrumbs(fn (Trail $trail) => $trail
            ->parent('platform.fsbo')
            ->push(__('Edit'), route('platform.fsbo.edit')));

    Route::screen('/viewing/{record?}', ViewingEditScreen::class)->name('platform.viewing.edit')
        ->breadcrumbs(fn (Trail $trail) => $trail
            ->parent('platform.viewing')
            ->push(__('Edit'), route('platform.viewing.edit')));

    Route::screen('/customer/{record?}', CustomerEditScreen::class)->name('platform.customer.edit')
        ->breadcrumbs(fn (Trail $trail) => $trail
            ->parent('platform.customer')
            ->push(__('Edit'), route('platform.customer.edit')));

    Route::screen('/marketing/{record?}', MarketingEditScreen::class)->name('platform.marketing.edit')
        ->breadcrumbs(fn (Trail $trail) => $trail
            ->parent('platform.marketing')
            ->push(__('Edit'), route('platform.marketing.edit')));

    Route::screen('/deed/{record?}', DeedEditScreen::class)->name('platform.deed.edit')
        ->breadcrumbs(fn (Trail $trail) => $trail
            ->parent('platform.deed')
            ->push(__('Edit'), route('platform.deed.edit')));

    Route::screen('/sale/{record?}', SaleEditScreen::class)->name('platform.sale.edit')
        ->breadcrumbs(fn (Trail $trail) => $trail
            ->parent('platform.sale')
            ->push(__('Edit'), route('platform.sale.edit')));
});
