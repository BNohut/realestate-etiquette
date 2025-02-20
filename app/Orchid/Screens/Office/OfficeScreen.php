<?php

namespace App\Orchid\Screens\Office;

use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;
use Orchid\Platform\Models\Role;

class OfficeScreen extends Screen
{
    public $offices;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $offices = Office::all();
        foreach ($offices as $office) {
            $office->load('attachment');
        }
        return [
            'offices' => $offices
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Offices';
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
                ->class('commandbar-add-button btn')
                ->route('platform.office.create')
                ->canSee(hasUserPermission('platform.offices.add')),
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
            Layout::view('Office/OfficeList')
        ];
    }

    public function remove(Request $request)
    {
        // Find Office
        $office = Office::findOrFail($request->officeId);

        // Manage Users of Office
        // SoftDelete OfficeManager
        // Find Office Manager
        $officeManager = User::join('role_users', 'users.id', '=', 'role_users.user_id')
            ->join('roles', 'role_users.role_id', '=', 'roles.id')
            ->where('roles.slug', 'ofis-yoneticisi')
            ->where('users.office_id', $request->officeId)->first();
        // Delete Office Manager Attachments
        $officeManager->attachment->each->delete();
        // Delete Office Manager - it will be soft deleted
        $officeManager->delete();

        // Change Office Consultants Role to Individual Consultants
        // Get Approved Consultants
        $approvedConsultants = User::join('role_users', 'users.id', '=', 'role_users.user_id')
            ->join('roles', 'role_users.role_id', '=', 'roles.id')
            ->where('roles.slug', 'ofis-danismani')
            ->where('office_id', $request->officeId)->whereNotNull('office_approved_at')->get();
        // Replace Role of Consultants
        foreach ($approvedConsultants as $consultant) {
            // Change User Role as 'Individual Consultant'
            $role = Role::where('slug', 'bireysel-danisman')->first();
            $consultant->replaceRoles(array($role->id));
            // Delete Office Information of Consultant & Save
            $consultant->office_id = null;
            $consultant->office_approved_at = null;
            $consultant->save();
            // Send Notification to Consultant
            Notification::send(
                $consultant,
                new SystemNotification(
                    "Ofis kapatıldı!",
                    $office->name . " kapatıldı. Artık bireysel danışman olarak devam edebilirsiniz.",
                    'platform.office'
                )
            );
        }
        // Get Consultants That Who Wanted To Join Office But Not Approved Yet
        $consultants = User::where('office_id', $request->officeId)->whereNull('office_approved_at')->get();
        foreach ($consultants as $consultant) {
            $consultant->office_id = null;
            $consultant->save();
        }

        // Get Office Assistants
        $officeAssistants = User::join('role_users', 'users.id', '=', 'role_users.user_id')
            ->join('roles', 'role_users.role_id', '=', 'roles.id')
            ->where('roles.slug', 'ofis-asistani')
            ->where('users.office_id', $request->officeId)->get();
        // Delete Office Assistant
        foreach ($officeAssistants as $assistant) {
            $assistant->delete();
        }
        // Delete Office Attachments
        $office->attachment->each->delete();
        // Delete Office
        $office->delete();

        Toast::info(__('Office deleted successfully'));
        return redirect()->back();
    }

    public function join(Request $request)
    {
        $user = $request->user();
        $user->office_id = $request->officeId;
        $user->save();

        //Send Notification to Office Manager
        //Find Office Object
        $office = Office::find($request->officeId);
        //Send Notification to Office Manager
        Notification::send(
            User::find($office->user_id),
            new SystemNotification(
                "Katılım isteği alındı!",
                $user->getFullNameAttribute() . " ofisinize katılma isteği gönderdi.",
                'platform.office.consultant'
            )
        );
        Toast::info(__('Join request sent successfully'));
        return redirect()->back();
    }

    public function cancelRequest(Request $request)
    {
        $user = $request->user();
        $user->office_id = null;
        $user->save();
        Toast::info(__('Join request cancelled successfully'));
        return redirect()->back();
    }

    public function leaveTheOffice(Request $request)
    {
        $user = $request->user();
        $user->office_id = null;
        $user->office_approved_at = null;
        $user->save();

        // Change User Role as 'Individual Consultant'
        $role = Role::where('slug', 'bireysel-danisman')->first();
        $user->replaceRoles(array($role->id));

        //Send Notification to Office Manager
        //Find Office Object
        $office = Office::find($request->officeId);
        //Send Notification to Office Manager
        Notification::send(
            User::find($office->user_id),
            new SystemNotification(
                "Aranızdan ayrılan oldu!",
                $user->getFullNameAttribute() . " ofisinizden ayrıldı.",
                'platform.office.consultant'
            )
        );
        Toast::info(__('You left the office successfully'));
        return redirect()->back();
    }
}
