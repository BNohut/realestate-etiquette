<?php

namespace App\Orchid\Screens\Config;

use App\Models\Setting;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Code;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class ConfigScreen extends Screen
{
    public $config;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'config' => json_encode(json_decode(Setting::first()->config), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),

        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Configuration');
    }

    public function description(): ?string
    {
        return __('Edit System Data');
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
                ->method('save')
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
            Layout::rows([
                Code::make('config')->lineNumbers()->height("100vh"),
            ])
        ];
    }

    public function save(Request $request)
    {
        $config = Setting::first();
        $config->config = $request->get('config');
        $config->save();
    }

    public function permission(): ?iterable
    {
        return [
            'platform.systems.settings',
        ];
    }
}
