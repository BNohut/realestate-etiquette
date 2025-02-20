<?php

namespace App\Orchid\Screens\Office;

use App\Models\User;
use App\Orchid\Layouts\Office\AssistantFormLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class AssistantEditScreen extends Screen
{
    public $user;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(User $user): iterable
    {
        return [
            'user' => $user,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return authUserInRole('ofis-asistani') ? __('Your Profile Information') : __('Edit Assistant') . " | " . $this->user->getFullNameAttribute();
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Save')->method('updateAssistant')->icon('save')->class('commandbar-save-button btn'),
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
            new AssistantFormLayout($this->user),
        ];
    }

    public function updateAssistant(User $user, Request $request)
    {
        $request->validate([
            'user.name' => 'required',
            'user.last_name' => 'required',
            'user.email' => 'required|email|unique:users,email,' . $user->id,
            'user.phone' => 'unique:users,phone,' . $user->id,
            'assistant.password' => 'nullable|confirmed',
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

        $user->fill($request->get('user'));
        $user->when($request->filled('assistant.password'), function (Builder $builder) use ($request) {
            $builder->getModel()->password = Hash::make($request->input('assistant.password'));
        });

        if (authUserInRole(['super-yonetici', 'yonetici'])) {
            $user->office_id = $request->get('user')['office_id'];
        }

        $user->save();

        Toast::info(__('Assistant updated successfully'), 'success');

        return redirect()->route('platform.office.assistant');
    }
}
