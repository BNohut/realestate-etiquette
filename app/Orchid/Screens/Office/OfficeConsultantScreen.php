<?php

namespace App\Orchid\Screens\Office;

use App\Models\Office;
use App\Models\User;
use App\Orchid\Layouts\Office\ConsultantListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;
use Orchid\Platform\Models\Role;

class OfficeConsultantScreen extends Screen
{
    public $users;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        if (authUserInRole(['super-yonetici', 'yonetici'])) {
            $users = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                ->join('roles', 'role_users.role_id', '=', 'roles.id')
                ->whereIn('roles.slug', ['ofis-danismani', 'bireysel-danisman'])->whereNotNull('office_id')->select('users.*')->paginate();
        } else {
            $authenticatedUserOfficeId = Office::firstWhere('user_id', auth()->user()->id)->id;
            $users = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                ->join('roles', 'role_users.role_id', '=', 'roles.id')
                ->whereIn('roles.slug', ['ofis-danismani', 'bireysel-danisman'])
                ->where('office_id', $authenticatedUserOfficeId)->select('users.*')->paginate();
        }
        return [
            'users' => $users,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Your Consultants';
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
            ConsultantListLayout::class,
        ];
    }

    public function approveJoinRequest(Request $request)
    {
        $user = User::find($request->userId);
        $user->office_approved_at = now();
        $user->save();
        // Change User Role as 'Office Consultant'
        $role = Role::where('slug', 'ofis-danismani')->first();
        $user->replaceRoles(array($role->id));

        //Send Notification to Request Owner
        //Find Office Object
        $office = Office::where('user_id', auth()->user()->id)->first();
        //Send Notification
        Notification::send(
            $user,
            new SystemNotification(
                "Katılım isteğiniz onaylandı!",
                $office->name . " katılım isteğinizi onayladı.",
                'platform.office'
            )
        );
        Toast::info(__('Join request approved successfully'));
        return redirect()->back();
    }

    public function rejectJoinRequest(Request $request)
    {
        $user = User::find($request->userId);
        $user->office_id = null;
        $user->save();
        //Send Notification to Request Owner
        //Find Office Object
        $office = Office::where('user_id', auth()->user()->id)->first();
        //Send Notification
        Notification::send(
            $user,
            new SystemNotification(
                "Katılım isteğiniz reddedildi!",
                $office->name . " katılım isteğinizi reddetti.",
                'platform.office'
            )
        );
        Toast::info(__('Join request rejected successfully'));
        return redirect()->back();
    }

    public function dismissConsultant(Request $request)
    {
        $user = User::find($request->userId);
        $office = Office::where('id', $user->office_id)->first();
        $user->office_id = null;
        $user->office_approved_at = null;
        $user->save();

        // Change User Role as 'Individual Consultant'
        $role = Role::where('slug', 'bireysel-danisman')->first();
        $user->replaceRoles(array($role->id));

        //Send Notification to Ex Office Consultant
        Notification::send(
            $user,
            new SystemNotification(
                "Çıkarma işlemi algılandı!",
                $office->name . " sizi danışmanları arasından çıkardı.",
                'platform.office'
            )
        );
        Toast::info(__('Consultant dismissed successfully'));
        return redirect()->back();
    }
}
