<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Province;

use App\Models\Province;
use App\Models\State;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ProvinceEditScreen extends Screen
{
    public $province;
    public $states;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Province $province): iterable
    {
        return [
            'province' => $province,
            'states' => $province->state()->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->province->name;
    }

    public function description(): ?string
    {
        return __('Province Edit');
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
            // Link::make('Sonraki')
            //     ->route('platform.province.edit', $this->province->id + 1),
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
                    Input::make('province.name')
                        ->type('text')
                        ->required()
                        ->title(__('Province Name')),
                    Input::make('province.phone_code')
                        ->type('number')
                        ->required()
                        ->title(__('Phone Code')),
                    Input::make('province.plate_number')
                        ->type('number')
                        ->required()
                        ->title(__('Plate Number')),
                    TextArea::make('province.tax_offices')
                        ->title(
                            __('Tax Offices'),
                        )->rows(5),
                    Input::make('province.id')
                        ->type('hidden')
                        ->value($this->province->id),
                    Button::make(__('Save'))
                        ->icon('check')
                        ->method('save'),
                ])->title(__('Province Info')),
                Layout::table('states', [
                    TD::make('name', __('State Name'))
                        ->render(function (State $state) {
                            return Link::make($state->name)->route('platform.state.edit', $state->id);
                        }),
                ])->title(__('States')),
            ])
        ];
    }

    public function save(Province $province, Request $request)
    {
        // $nextid = $request->get('province')['id'];
        // $nextid++;
        $province->fill($request->get('province'))->update();
        Toast::message(__('Record updated.'));
        // return redirect()->route('platform.province.edit', (string) $nextid);
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.provinces',
        ];
    }
}
