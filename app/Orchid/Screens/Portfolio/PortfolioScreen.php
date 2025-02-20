<?php

namespace App\Orchid\Screens\Portfolio;

use App\Models\Portfolio;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class PortfolioScreen extends Screen
{
    public $portfolios;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'portfolios' => Portfolio::all(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Portfolios');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->class('commandbar-add-button btn')
                ->icon('plus')
                ->route('platform.portfolio.edit')
                ->canSee(hasUserPermission('platform.portfolios.add')),
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
            Layout::view('Portfolio/PortfolioList')
        ];
    }

    public function permission(): ?iterable
    {
        return [
            'platform.portfolios'
        ];
    }
}
