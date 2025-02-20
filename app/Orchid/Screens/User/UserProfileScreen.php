<?php

declare(strict_types=1);

namespace App\Orchid\Screens\User;

use App\Models\State;
use App\Orchid\Layouts\User\ProfilePasswordLayout;
use App\Orchid\Layouts\User\UserEditLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Orchid\Layouts\ProvinceStateNeighborhood;
use Orchid\Access\Impersonation;
use Orchid\Attachment\File;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class UserProfileScreen extends Screen
{
    public $user;
    public $province;
    public $state;
    public $neighborhoods;
    public $neighborhood;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @param Request $request
     *
     * @return array
     */
    public function query(): iterable
    {
        $user = request()->user();
        $user->json = $user->json != null ? json_decode($user->json, true) : null;
        $neighborhoodOptions = [];
        if ($user->province_id) {
            $neighborhoodsList = explode(", ", State::find($user->state_id)->neighborhoods);
            foreach ($neighborhoodsList as $key => $value) {
                $neighborhoodOptions[$value] = $value;
            }
        }
        return [
            'user' => $user,
            'province' => $user->province_id,
            'state' => $user->state_id,
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
        return 'My account';
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Update your account details such as name, email address and password';
    }

    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Back to my account')
                ->canSee(Impersonation::isSwitch())
                ->class('btn btn-primary')
                ->icon('bs.people')
                ->route('platform.switch.logout'),

            Button::make('Sign out')
                ->class('btn btn-warning')
                ->icon('bs.box-arrow-left')
                ->route('platform.logout'),
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
                ->description(__("Update your account's profile information and email address."))
                ->commands(
                    Button::make(__('Save'))
                        ->type(Color::DEFAULT())
                        ->icon('check')
                        ->method('save')
                ),

            Layout::block(ProfilePasswordLayout::class)
                ->title(__('Update Password'))
                ->description(__('Ensure your account is using a long, random password to stay secure.'))
                ->commands(
                    Button::make(__('Update password'))
                        ->type(Color::DEFAULT())
                        ->icon('check')
                        ->method('changePassword')
                ),
        ];
    }

    /**
     * @param Request $request
     */
    public function save(Request $request): void
    {
        $this->user = $request->user();
        $request->validate([
            'user.name' => 'required|string',
            'user.email' => [
                'required',
                Rule::unique(User::class, 'email')->ignore($request->user()),
            ],
        ]);

        if ($request->file('avatar')) {
            //If contact has old avatar, delete it
            if ($this->user->attachment()->first()) {
                $this->user->attachment[0]->delete();
            }
            //Get Request Contact Array
            $requestUser = $request->get('user');
            //Transform
            $file = new File($request->file('avatar'));
            $attachment = $file->load();
            $requestUser['avatar'] = $attachment;
            //Merge All To Request
            $request->merge(['user' => $requestUser]);
            //Save
            $this->user
                ->fill($request->collect('user')->except(["avatar"])->toArray())
                ->fill(['avatar' => $attachment->id]);

            $this->user->attachment()->syncWithoutDetaching(
                $request->input('user.avatar', [])
            );
        } else {
            $this->user
                ->fill($request->collect('user')->except(['password', 'permissions', 'roles'])->toArray());
        }
        $this->user->province_id = $request->get('province');
        $this->user->state_id = $request->get('state');
        $this->user->neighborhood = $request->get('neighborhood');
        $this->user->save();

        Toast::info(__('Profile updated.'));
    }

    /**
     * @param Request $request
     */
    public function changePassword(Request $request): void
    {
        $guard = config('platform.guard', 'web');
        $request->validate([
            'old_password' => 'required|current_password:' . $guard,
            'password' => 'required|confirmed',
        ]);

        tap($request->user(), function ($user) use ($request) {
            $user->password = Hash::make($request->get('password'));
        })->save();

        Toast::info(__('Password changed.'));
    }
}
