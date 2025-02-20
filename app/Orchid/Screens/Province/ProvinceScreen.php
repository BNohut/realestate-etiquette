<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Province;

use App\Models\Province;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class ProvinceScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            "provinces" => Province::filters()->defaultSort('id')->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Provinces');
    }

    public function description(): ?string
    {
        return __('Province Management');
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
                ->modal('newProvinceModal')
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            Layout::table('provinces', [
                TD::make('name', __('Province Name'))->sort()->filter()->render(
                    fn (Province $province)
                    => Link::make($province->name)
                        ->route('platform.province.edit', $province->id)->id('province-list-name-link')
                ),
                TD::make('plate_number', __('Plate Number'))->sort()->filter()->render(fn (Province $province) => Label::make()->value($province->plate_number)->style('color: #fff !important;')->class('mb-0')),
                TD::make('phone_code', __('Phone Code'))->sort()->filter()->render(fn (Province $province) => Label::make()->value($province->phone_code)->style('color: #fff !important;')->class('mb-0')),
                TD::make('state.name', __('States'))->width('30%')->render(function (Province $province) {
                    $return = [];
                    foreach ($province->state as $state) {
                        $return[] = $state->name;
                    }
                    return Label::make()->value(implode(", ", $return))->style('color: #fff !important;')->class('mb-0');
                }),
                TD::make('tax_office', __('Tax Offices'))->width('30%')->render(function (Province $province) {
                    return Label::make()->value($province->tax_offices)->style('color: #fff !important;')->class('mb-0');
                }),
            ]),
            Layout::modal('newProvinceModal', [
                Layout::rows([
                    Input::make('province.name')->title(__('Province Name')),
                    Input::make('province.plate_number')->title(__('Plate Number')),
                    Input::make('province.phone_code')->title(__('Phone Code')),
                ]),
            ])->applyButton(__('Save'))->title(__('Add New Province')),
        ];
    }

    public function create(Request $request)
    {
        $province = new Province();
        $province->fill($request->get('province'));
        // $province->phone_code = "[" . json_encode($request->get('province')['phone_code']) . "]";
        $province->save();
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.provinces',
        ];
    }
}
