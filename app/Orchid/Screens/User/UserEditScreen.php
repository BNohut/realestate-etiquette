<?php

declare(strict_types=1);

namespace App\Orchid\Screens\User;

use App\Models\State;
use App\Orchid\Layouts\Role\RolePermissionLayout;
use App\Orchid\Layouts\User\UserEditLayout;
use App\Orchid\Layouts\User\UserPasswordLayout;
use App\Orchid\Layouts\User\UserRoleLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Orchid\Access\Impersonation;
// use Orchid\Platform\Models\User;
use App\Models\User;
use App\Orchid\Layouts\ProvinceStateNeighborhood;
use Orchid\Attachment\File;
use Orchid\Platform\Models\Role;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class UserEditScreen extends Screen
{
    /**
     * @var User
     */
    public $user;
    public $province;
    public $state;
    public $neighborhoods;
    public $neighborhood;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @param User $user
     *
     * @return array
     */
    public function query(User $user): iterable
    {
        $authUser = auth()->user();
        $user->load(['roles', 'attachment']);
        $user->json = $user->json != null ? json_decode($user->json, true) : null;
        $neighborhoodOptions = [];
        if (isset($user->province_id) || isset($authUser->province_id)) {
            $neighborhoodsList = explode(", ", State::find(isset($user->state_id) ? $user->state_id : $authUser->state_id)->neighborhoods);
            foreach ($neighborhoodsList as $key => $value) {
                $neighborhoodOptions[$value] = $value;
            }
        }

        if (!isset($user->id)) {
            $user->visibility = 1;
        }
        return [
            'user' => $user,
            'permission' => $user->getStatusPermission(),
            'province' => isset($user->province_id) ? $user->province_id : $authUser->province_id,
            'state' => isset($user->state_id) ? $user->state_id : $authUser->state_id,
            'neighborhood' => $user->neighborhood,
            'neighborhoods' => $neighborhoodOptions,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return isset($this->user->id) ? 'Edit User' : 'Create User';
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Details such as name, email and password';
    }

    /**
     * @return iterable|null
     */
    public function permission(): ?iterable
    {
        return [
            'platform.systems.users',
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Impersonate user'))
                ->icon('login')
                ->class('btn btn-primary')
                ->confirm(__('You can revert to your original state by logging out.'))
                ->method('loginAs')
                ->canSee(isset($this->user->id) && \request()->user()->id !== $this->user->id),

            Button::make(__('Save'))
                ->class('commandbar-save-button btn')
                ->icon('save')
                ->method('save'),

            Button::make(__('Remove'))
                ->class('btn btn-danger')
                ->icon('trash')
                ->confirm(__('Once the account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                ->method('remove')
                ->canSee(isset($this->user->id)),

            Button::make(__('Approve User'))
                ->class('btn btn-info')
                ->method('approveUser')
                ->canSee(isset($this->user->id) && $this->user->email_verified_at == null)
        ];
    }

    /**
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [

            Layout::block([
                Layout::view('partials/ProfilePhoto', ['edit' => true, 'contact' => $this->user, 'contactModal' => false, 'firstCall' => false]),
                ProvinceStateNeighborhood::class, // Listener
                UserEditLayout::class,
            ])
                ->title(__('Profile Information'))
                ->description(__('Update your account\'s profile information and email address.'))
                ->commands(
                    Button::make(__('Save'))
                        ->type(Color::DEFAULT())
                        ->icon('check')
                        ->canSee(isset($this->user->id))
                        ->method('save')
                ),

            Layout::block(UserPasswordLayout::class)
                ->title(__('Password'))
                ->description(__('Ensure your account is using a long, random password to stay secure.'))
                ->commands(
                    Button::make(__('Save'))
                        ->type(Color::DEFAULT())
                        ->icon('check')
                        ->canSee(isset($this->user->id))
                        ->method('save')
                ),

            Layout::block(UserRoleLayout::class)
                ->title(__('Roles'))
                ->description(__('A Role defines a set of tasks a user assigned the role is allowed to perform.'))
                ->commands(
                    Button::make(__('Save'))
                        ->type(Color::DEFAULT())
                        ->icon('check')
                        ->canSee(isset($this->user->id))
                        ->method('save')
                ),

            Layout::block(RolePermissionLayout::class)
                ->title(__('Permissions'))
                ->description(__('Allow the user to perform some actions that are not provided for by his roles'))
                ->commands(
                    Button::make(__('Save'))
                        ->type(Color::DEFAULT())
                        ->icon('check')
                        ->canSee(isset($this->user->id))
                        ->method('save')
                ),

        ];
    }

    /**
     * @param User    $user
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(User $user, Request $request)
    {

        if ($request->has('user.roles') && in_array(Role::firstWhere('slug', 'ofis-danismani')->id, $request->input('user.roles'))) {
            if (!$request->input('user.office_id')) {
                return redirect()->back()->withErrors(['user.office_id' => __('Please select an office.')]);
            }
        }
        if ($request->has('user.office_id') && in_array(Role::firstWhere('slug', 'bireysel-danisman')->id, $request->input('user.roles'))) {
            return redirect()->back()->withErrors(['user.office_id' => __('You can not select an office for individual consultant.')]);
        }
        $request->validate([
            'user.email' => [
                'required',
                Rule::unique(User::class, 'email')->ignore($user),
            ],
        ]);

        $permissions = collect($request->get('permissions'))
            ->map(fn ($value, $key) => [base64_decode($key) => $value])
            ->collapse()
            ->toArray();

        $user->when($request->filled('user.password'), function (Builder $builder) use ($request) {
            $builder->getModel()->password = Hash::make($request->input('user.password'));
        });

        //Get Request Contact Array
        $requestUser = $request->get('user');

        if ($request->has('user.json')) {
            $requestUser['json'] = json_encode($request->get('user')['json']);
        }

        if ($request->file('avatar')) {
            //If contact has old avatar, delete it
            if ($user->attachment()->first()) {
                $user->attachment[0]->delete();
            }

            //Transform
            $file = new File($request->file('avatar'));
            $attachment = $file->load();
            $requestUser['avatar'] = $attachment;
            //Merge All To Request
            $request->merge(['user' => $requestUser]);
            //Save
            $user
                ->fill($request->collect('user')->except(['password', 'permissions', 'roles', "avatar"])->toArray())
                ->fill(['avatar' => $attachment->id])
                ->save();

            $user->attachment()->syncWithoutDetaching(
                $request->input('user.avatar', [])
            );
        } else {
            $user
                ->fill($request->collect('user')->except(['password', 'permissions', 'roles'])->toArray());
        }

        $user->province_id = $request->get('province');
        $user->state_id = $request->get('state');
        $user->neighborhood = $request->get('neighborhood');
        if ($user->office_id && $user->office_approved_at == null) {
            $user->office_approved_at = now();
        }
        $user
            ->fill(['permissions' => $permissions])
            ->save();

        $user->replaceRoles($request->input('user.roles'));

        Toast::info(__('You have successfully updated a user.'));

        return redirect()->route('platform.systems.users');
    }

    /**
     * @param User $user
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(User $user)
    {
        $user->delete();

        Toast::info(__('User was removed'));

        return redirect()->route('platform.systems.users');
    }

    /**
     * @param User $user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginAs(User $user)
    {
        Impersonation::loginAs($user);

        Toast::info(__('You are now impersonating this user'));

        return redirect()->route(config('platform.index'));
    }

    public function approveUser(User $user)
    {
        $user->email_verified_at = date('Y-m-d H:i:s');
        $user->save();
        return redirect()->back();
    }
}
