<?php

namespace App\Orchid\Screens\Consultant;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Mail\Markdown;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class ConsultantDetailScreen extends Screen
{
    public $consultant;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $consultant = User::find(request()->consultant);
        $consultant->load('attachment');
        $consultant->json = $consultant->json != null ? json_decode($consultant->json, true) : null;
        $htmlContent = "";
        if ($consultant->about_me) {
            $parseedAboutMe = Markdown::parse($consultant->about_me);
            $pattern = '/!\[.*?\]\((.*?)\)/';
            $replacement = '<img src="$1">' . "<br>";
            $htmlContent = preg_replace($pattern, $replacement, $parseedAboutMe);
        }
        return [
            'consultant' => $consultant,
            'consultants' => User::all(),
            'aboutMe' => $htmlContent,
            'portfolios' => Portfolio::where('user_id', $consultant->id)->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Consultant Information');
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
            Layout::view('Consultant/ConsultantDetail')
        ];
    }

    public function permission(): ?iterable
    {
        return [
            'platform.consultants.detail',
        ];
    }
}
