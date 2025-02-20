<?php

namespace App\Orchid\Screens\Office;

use App\Models\User;
use App\Orchid\Layouts\Office\AssistantFormLayout;
use App\Orchid\Layouts\Office\AssistantListLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Orchid\Platform\Models\Role;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class AssistantScreen extends Screen
{
    public $assistant;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        if (authUserInRole(['super-yonetici', 'yonetici'])) {
            $users = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                ->join('roles', 'role_users.role_id', '=', 'roles.id')
                ->where('roles.slug', 'ofis-asistani')->select('users.*')->paginate();
        } else {
            $users = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                ->join('roles', 'role_users.role_id', '=', 'roles.id')
                ->where('roles.slug', 'ofis-asistani')
                ->where('office_id', auth()->user()->office_id)->select('users.*')->paginate();
        }
        return [
            'users' => $users,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Your Assistants';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Add')
                ->class('commandbar-add-button btn')
                ->icon('plus')
                ->modal('assistantModal')
                ->method('create')
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::modal('assistantModal', [
                AssistantFormLayout::class,
            ])->title(__('Add Assistant'))->withoutCloseButton()->applyButton(__('Save')),
            AssistantListLayout::class,
        ];
    }

    public function create(Request $request)
    {

        $request->validate([
            'user.name' => 'required',
            'user.last_name' => 'required',
            'user.email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->whereNull('deleted_at'), // Soft deleted kayıtları dikkate alma
            ],
            'user.phone' => Rule::unique('users', 'phone')->whereNull('deleted_at'), // Soft deleted kayıtları dikkate alma
            'assistant.password' => 'required|confirmed',
        ], [
            'user.name.required' => __('Name is required'),
            'user.last_name.required' => __('Last Name is required'),
            'user.email.required' => __('Email is required'),
            'user.email.email' => __('Email is not valid'),
            'user.email.unique' => __('Email is already taken'),
            'user.phone.unique' => __('Phone is already taken'),
            'user.password.required' => __('Password is required'),
            'assistant.password.confirmed' => __('Password confirmation does not match'),
        ]);
        $isThereAnySoftDeletedUserWithThatEmail = User::onlyTrashed()->where('email', $request->get('user')['email'])->first();
        if ($isThereAnySoftDeletedUserWithThatEmail) {
            $user = $isThereAnySoftDeletedUserWithThatEmail;
            $user->restore();
            $restore = true;
        } else {
            $user = new User();
            $restore = false;
        }

        $user->fill($request->get('user'));
        $user->when($request->filled('assistant.password'), function (Builder $builder) use ($request) {
            $builder->getModel()->password = Hash::make($request->input('assistant.password'));
        });

        if (authUserInRole('ofis-yoneticisi')) {
            $user->office_id = auth()->user()->office_id;
        }
        if (authUserInRole(['super-yonetici', 'yonetici'])) {
            $user->office_id = $request->get('user')['office_id'];
        }
        $user->email_verified_at = now();
        $user->office_approved_at = now();
        $user->save();

        $role = Role::firstWhere('slug', 'ofis-asistani');
        $user->roles()->attach($role);

        $restore ? Toast::info(__('Assistant restored successfully')) :
            Toast::info(__('Assistant created successfully'));

        return redirect()->route('platform.office.assistant');
    }

    public function dismissAssistant(Request $request)
    {
        $user = User::find($request->userId);
        $user->roles()->detach();
        $user->delete();

        Toast::info(__('Assistant dismissed successfully'));

        return redirect()->route('platform.office.assistant');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.myoffice'
        ];
    }
}
