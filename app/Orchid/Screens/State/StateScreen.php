<?php

declare(strict_types=1);

namespace App\Orchid\Screens\State;

use App\Models\State;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use App\Models\Province;
use App\Orchid\Filters\ProvinceFilter;
use App\Orchid\Layouts\Province\ProvinceFilterLayout;
use Orchid\Screen\Fields\Label;

class StateScreen extends Screen
{

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(State $state): array
    {
        return [
            "state" => $state->with('province')->filters([ProvinceFilter::class])->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('States');
    }

    public function description(): ?string
    {
        return __('State Management');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make(__('Add'))
                ->class('commandbar-add-button btn')
                ->icon('plus')
                ->method('create')
                ->modal('newStateModal')
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

            ProvinceFilterLayout::class,
            Layout::table('state', [
                TD::make('name', __('Name'))->sort()->filter()
                    ->render(function (State $state) {
                        return Link::make($state->name)->route('platform.state.edit', $state->id)->id('state-list-name-link');
                    }),
                TD::make('province.name', __('Province'))
                    ->render(function (State $state) {
                        return Label::make()->value($state->province->name)->style('color: #fff !important;')->class('mb-0');
                    })
            ]),
            Layout::modal('newStateModal', [
                Layout::rows([
                    Input::make('state.name')->title(__('State Name')),
                    Select::make('state.province_id')->fromModel(Province::class, 'name')->empty(__('No select'))
                ]),
            ])->applyButton(__('Save'))->title(__('Add New State')),
        ];
    }
    public function create(State $state, Request $request)
    {
        $state->fill($request->get('state'))->save();
        Toast::message(__('Created'));
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.states',
        ];
    }
}
