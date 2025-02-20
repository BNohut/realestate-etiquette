<?php

declare(strict_types=1);

namespace App\Orchid\Screens\State;

use App\Models\Province;
use App\Models\State;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class StateEditScreen extends Screen
{
    public $state;
    public $states;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(State $state): iterable
    {
        return [
            'state' => $state,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->state->name;
    }

    public function description(): ?string
    {
        return __('State Edit');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            // Button::make(__('Save'))
            //     ->icon('check')
            //     ->method('save'),
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
            Layout::columns([
                Layout::rows([
                    Input::make('state.name')
                        ->type('text')
                        ->required()
                        ->title(__('State Name')),
                    Select::make('state.province_id')
                        ->fromModel(Province::class, 'name')
                        ->title(__('Province Name')),
                    TextArea::make('state.neighborhoods')
                        ->title(__('Neighbors'))
                        ->rows(5),
                    TextArea::make('state.zip_codes')
                        ->title(__('Zip Codes'))
                        ->rows(5),
                    Button::make(__('Save'))
                        ->icon('check')
                        ->method('save'),
                ])->title(__('State Info'))
            ]),
        ];
    }

    public function save(State $state, Request $request)
    {
        $state->fill($request->get('state'))->update();
        Toast::message(__('Record updated.'));
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.states',
        ];
    }
}
