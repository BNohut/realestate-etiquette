<?php

namespace App\Orchid\Screens\Portfolio;

use App\Models\Portfolio;
use Illuminate\Mail\Markdown;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class PortfolioDetailScreen extends Screen
{
    public $portfolio;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Portfolio $portfolio): iterable
    {
        $htmlContent = "";
        if ($portfolio->description) {
            $parsedAboutMe = Markdown::parse($portfolio->description);
            $pattern = '/!\[.*?\]\((.*?)\)/';
            $replacement = '<img src="$1">' . "<br>";
            $htmlContent = preg_replace($pattern, $replacement, $parsedAboutMe);
        }
        return [
            'portfolio' => $portfolio,
            'description' => $htmlContent,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Portfolio Detail') . " | " . $this->portfolio->portfolio_no;
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::view('Portfolio/PortfolioDetail'),
        ];
    }

    public function permission(): ?iterable
    {
        return [
            'platform.portfolios.detail'
        ];
    }
}
