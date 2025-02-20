<?php

namespace App\Orchid\Screens\Consultant;

use App\Models\Follower;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ConsultantScreen extends Screen
{
    public $consultants;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'consultants' => User::leftJoin('role_users', 'users.id', '=', 'role_users.user_id')
                ->leftJoin('roles', 'role_users.role_id', '=', 'roles.id')
                ->whereIn('roles.slug', ['bireysel-danisman', 'ofis-danismani'])
                ->select('users.id', 'users.name', 'users.last_name', 'users.email', 'users.phone', 'users.visibility', 'users.url', 'users.province_id', 'users.state_id', 'users.neighborhood', 'users.json', 'users.avatar', 'users.office_id', 'users.permissions', 'users.about_me')
                ->orderBy('users.name', 'asc')
                ->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Consultants');
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
                ->route('platform.systems.users.create')
                ->icon('plus')
                ->canSee(canUserDelete())

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
            Layout::view('Consultant/ConsultantList')
        ];
    }

    public function permission(): ?iterable
    {
        return [
            'platform.consultants',
        ];
    }

    public function follow(Request $request)
    {
        $from = auth()->user()->id;
        $to = $request->consultant_id;;
        $newFollowRecord = Follower::create([
            'from' => $from,
            'to' => $to,
            'approved' => null,
        ]);
        if (!$newFollowRecord) {
            Toast::error(__('Something went wrong'));
            return redirect()->back();
        }
        //Send Notification to Following Consultant
        Notification::send(
            User::find($to),
            new SystemNotification(
                "Takip isteği alındı!",
                $newFollowRecord->from_name . " size takip isteği gönderdi.",
                'platform.consultant'
            )
        );

        Toast::info(__('You sent a follow request the consultant'));
        return redirect()->back();
    }

    public function cancelRequest(Request $request)
    {
        $from = auth()->user()->id;
        $to = $request->consultant_id;
        $followRecord = Follower::where('from', $from)
            ->where('to', $to)
            ->first();
        if (!$followRecord) {
            Toast::error(__('Something went wrong'));
            return redirect()->back();
        }
        $followRecord->delete();
        Toast::info(__('You canceled your follow request'));
        return redirect()->back();
    }

    public function approve(Request $request)
    {
        $from = $request->consultant_id;
        $to = auth()->user()->id;
        $followRecord = Follower::where('from', $from)
            ->where('to', $to)
            ->first();
        if (!$followRecord) {
            Toast::error(__('Something went wrong'));
            return redirect()->back();
        }
        $followRecord->approved = date('Y-m-d H:i:s');
        $followRecord->save();

        //Send Notification to Follower Consultant
        Notification::send(
            User::find($from),
            new SystemNotification(
                "Takip isteği onaylandı!",
                $followRecord->to_name . " takip isteğinizi onayladı.",
                'platform.consultant'
            )
        );
        Toast::info(__('You approved the follow request'));
        return redirect()->back();
    }

    public function reject(Request $request)
    {
        $from = $request->consultant_id;
        $to = auth()->user()->id;
        $followRecord = Follower::where('from', $from)
            ->where('to', $to)
            ->first();
        if (!$followRecord) {
            Toast::error(__('Something went wrong'));
            return redirect()->back();
        }
        $followRecord->delete();
        Toast::info(__('You rejected the follow request'));
        return redirect()->back();
    }

    public function followBack(Request $request)
    {
        $from = auth()->user()->id;
        $to = $request->consultant_id;
        $newFollowRecord = Follower::create([
            'from' => $from,
            'to' => $to
        ]);
        if (!$newFollowRecord) {
            Toast::error(__('Something went wrong'));
            return redirect()->back();
        }

        //Send Notification to Follower Consultant
        Notification::send(
            User::find($to),
            new SystemNotification(
                "Takip isteği alındı!",
                "Takip ettiğiniz " . $newFollowRecord->from_name . " de size takip isteği gönderdi.",
                'platform.consultant'
            )
        );

        Toast::info(__('You sent a follow request the consultant'));
        return redirect()->back();
    }

    public function unfollow(Request $request)
    {
        $from = auth()->user()->id;
        $to = $request->consultant_id;
        $followRecord = Follower::where('from', $from)
            ->where('to', $to)
            ->first();
        if (!$followRecord) {
            Toast::error(__('Something went wrong'));
            return redirect()->back();
        }
        $followRecord->delete();
        Toast::info(__('You unfollowed the consultant'));
        return redirect()->back();
    }
}
