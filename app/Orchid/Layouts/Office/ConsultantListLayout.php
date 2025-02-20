<?php

namespace App\Orchid\Layouts\Office;

use App\Models\Office;
use App\Models\User;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ConsultantListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'users';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('office_id', __('Office'))->render(function (User $user) {
                return Label::make()->value(Office::find($user->office_id)->name)->style('color: #fff !important;')->class('mb-0');
            })->canSee(authUserInRole(['super-yonetici', 'yonetici'])),
            TD::make('name', __('Consultant'))->render(function (User $user) {
                return Link::make($user->name . ' ' . $user->last_name)->style('padding-left: 0 !important;')->id('consultant-list-name-link')
                    ->route('platform.consultant.detail', $user->id);
            }),
            TD::make('role', __('Role'))->render(function (User $user) {
                return Label::make()->value($user->roles[0]->name)->style('color: #fff !important;')->class('mb-0');
            }),
            TD::make('status', __('Status'))->render(function (User $user) {
                return Label::make()->value($user->office_approved_at ? __('Approved') : __('Pending'))->style('color: #fff !important;')->class('mb-0');
            }),
            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('20%')
                ->render(function (User $user) {
                    if ($user->office_approved_at) {
                        return
                            Button::make(__('Dismiss'))
                            ->method('dismissConsultant', ['userId' => $user->id])
                            ->class('btn btn-danger justify-content-center small')
                            ->confirm(__('Are you sure you want to dismiss this consultant?'));
                    }
                    return Group::make([
                        Button::make(__('Approve'))
                            ->method('approveJoinRequest', ['userId' => $user->id])
                            ->class('btn btn-success justify-content-center small')
                            ->confirm(__('Are you sure you want to approve this join request?')),
                        Button::make(__('Reject'))
                            ->method('rejectJoinRequest', ['userId' => $user->id])
                            ->class('btn btn-danger justify-content-center small')
                            ->confirm(__('Are you sure you want to reject this join request?')),
                    ]);
                }),
        ];
    }
}
