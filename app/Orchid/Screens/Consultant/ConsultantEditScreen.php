<?php

namespace App\Orchid\Screens\Consultant;

use App\Models\Province;
use App\Models\State;
use App\Models\User;
use App\Orchid\Layouts\ProvinceStateNeighborhood;
use App\Orchid\Layouts\User\UserEditLayout;
use Illuminate\Database\Eloquent\Builder;
use Orchid\Support\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Orchid\Attachment\File;
use Orchid\Platform\Models\Role;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;


class ConsultantEditScreen extends Screen
{
    public $user;
    public $province;
    public $state;
    public $neighborhoods;
    public $neighborhood;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Request $request): iterable
    {
        $consultant = $request->consultant ? User::find($request->consultant) : new User();
        $consultant->json = $consultant->json != null ? json_decode($consultant->json, true) : null;
        $neighborhoodOptions = [];
        if ($consultant->province_id) {
            $neighborhoodsList = explode(", ", State::find($consultant->state_id)->neighborhoods);
            foreach ($neighborhoodsList as $key => $value) {
                $neighborhoodOptions[$value] = $value;
            }
        }
        return [
            'user' => $consultant,
            'province' => $consultant->province_id,
            'state' => $consultant->state_id,
            'neighborhood' => $consultant->neighborhood,
            'neighborhoods' => $neighborhoodOptions,
        ];
    }

    // public function query(Request $request): iterable
    // {

    //     $authUser = auth()->user();
    //     $consultant = $request->consultant ? User::find($request->consultant) : new User();
    //     $consultant->json = $consultant->json != null ? json_decode($consultant->json, true) : null;
    //     $neighborhoodOptions = [];
    //     if (isset($consultant->province_id) || isset($authUser->province_id)) {
    //         $neighborhoodsList = explode(", ", State::find(isset($consultant->state_id) ? $consultant->state_id : $authUser->state_id)->neighborhoods);
    //         foreach ($neighborhoodsList as $key => $value) {
    //             $neighborhoodOptions[$value] = $value;
    //         }
    //     }
    //     return [
    //         'user' => $consultant,
    //         'province' => isset($consultant->province_id) ? $consultant->province_id : $authUser->province_id,
    //         'state' => isset($consultant->state_id) ? $consultant->state_id : $authUser->state_id,
    //         'neighborhood' => $consultant->neighborhood,
    //         'neighborhoods' => $neighborhoodOptions,
    //     ];
    // }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->user->name ? __('Edit Consultant') . " | " . $this->user->name : __('Add Consultant');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Save'))
                ->class('commandbar-save-button btn')
                ->icon('save')
                ->method('save'),
            Button::make(__('Delete'))
                ->icon('trash')
                ->method('deleteConsultant')
                ->class('btn btn-danger')
                ->confirm(__('Are you sure you want to delete this consultant?'))
                ->canSee($this->user->name != null && canUserDelete()),
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
            Layout::block([
                Layout::view('partials/ProfilePhoto', ['edit' => true, 'contact' => $this->user, 'contactModal' => false, 'firstCall' => false]),
                ProvinceStateNeighborhood::class, // Listener
                UserEditLayout::class,
            ])
                ->title(__('Consultant Information'))
                ->description(__('Update consultan\'s profile information'))
                ->commands(
                    Button::make(__('Save'))
                        ->icon('save')
                        ->id('saveConsultantBottomButton')
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
    public function save(Request $request)
    {
        $user = $this->user;
        $request->validate([
            'user.email' => [
                'required',
                Rule::unique(User::class, 'email')->ignore($user),
            ],
        ]);
        if (!isset($user->id)) {
            $user->when(!$request->filled('user.password'), function (Builder $builder) {
                $builder->getModel()->password = Hash::make('consultant');
            });
        }

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
        $user->save();
        if ($user->getRoles()->count() == 0) {
            $role = Role::firstWhere('slug', 'bireysel-danisman');
            $user->roles()->attach($role);
        }


        Toast::info(__('You have successfully updated a user.'));

        return redirect()->route('platform.consultant');
    }


    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteConsultant()
    {
        $this->user->delete();
        Toast::info(__('You have successfuly deleted the user.'));
        return redirect()->route('platform.consultant');
    }

    public function permission(): ?iterable
    {
        return [
            'platform.consultants.edit',
        ];
    }
}
