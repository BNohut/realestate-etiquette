<?php

namespace App\Orchid\Screens\Deed;

use App\Models\Record;
use App\Models\RecordType;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Orchid\Layouts\Deed\DeedListLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class DeedManagementScreen extends Screen
{
    public $records;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $deedType = RecordType::firstWhere('name', 'Tapu Satış-Kiralama İşlemleri')->id;
        $recordsQuery = Record::join('users', 'users.id', 'records.user_id')
            ->whereNotNull(['users.office_id', 'users.office_approved_at'])
            ->where('record_type_id', $deedType)
            ->withTrashed()
            ->orderBy('record_date', 'desc');
        if (authUserInRole(['ofis-yoneticisi'])) {
            $recordsQuery->where('users.office_id', auth()->user()->office_id);
        }
        $records = $recordsQuery->select('records.*')->paginate(10);
        $records->each(function ($record) {
            $record->append('office_name');
        });
        return [
            'records' =>  $records,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Deed Sale-Rent Management';
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
            DeedListLayout::class,
        ];
    }

    public function approve(Request $request)
    {
        $record = Record::find($request->recordId);
        $record->approved_at = now();
        $recordUserId = $record->userS->id;
        $record->save();
        Notification::send(
            User::find($recordUserId),
            new SystemNotification(
                __('Deed Sale-Rent Record Approved'),
                __('Your deed sale-rent record has been approved by the office manager.'),
                'platform.deed'
            )
        );
        Toast::info(__('Record Approved Successfully'));
        return redirect()->back();
    }

    public function reject(Request $request)
    {
        $record = Record::find($request->recordId);
        $recordUserId = $record->userS->id;
        $record->delete();
        Notification::send(
            User::find($recordUserId),
            new SystemNotification(
                __('Deed Sale-Rent Record Rejected'),
                __('Your deed sale-rent record has been rejected by the office manager.'),
                'platform.deed'
            )
        );
        Toast::info(__('Record Deleted Successfully'));
        return redirect()->back();
    }
}
