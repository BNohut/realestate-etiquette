<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Follower;
use App\Models\Portfolio;
use App\Models\Record;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MainController
{
    public function consultants()
    {
        try {
            if (authUserInRole(['super-yonetici', 'yonetici'])) {
                $users = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                    ->join('roles', 'role_users.role_id', '=', 'roles.id')
                    ->whereIn('roles.slug', ['ofis-danismani', 'bireysel-danisman'])
                    ->select('users.id', 'users.name', 'users.last_name')
                    ->get();
            }
            if (authUserInRole('ofis-yoneticisi')) {
                $users = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                    ->join('roles', 'role_users.role_id', '=', 'roles.id')
                    ->where('roles.slug', 'ofis-danismani')
                    ->where('users.office_id', auth()->user()->office_id)
                    ->select('users.id', 'users.name', 'users.last_name')
                    ->get();
            }
            if (authUserInRole('ofis-danismani')) {
                $officeConsultantIds = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                    ->join('roles', 'role_users.role_id', '=', 'roles.id')
                    ->where('roles.slug', 'ofis-danismani')
                    ->where('office_id', auth()->user()->office_id)
                    ->where('users.id', '!=', auth()->user()->id)
                    ->select('users.id')
                    ->get()
                    ->pluck('id')
                    ->toArray();
                $followingsIds = Follower::where('from', auth()->user()->id)
                    ->whereNotNull('approved')
                    ->groupBy('followers.from', 'followers.to')
                    ->pluck('to')
                    ->toArray();
                $users = User::whereIn('id', array_unique(array_merge($followingsIds, $officeConsultantIds)))
                    ->orderBy('name', 'asc')
                    ->get('id', 'name', 'last_name');
            }
            if (authUserInRole('bireysel-danisman')) {
                $followingsIds = Follower::where('from', auth()->user()->id)
                    ->whereNotNull('approved')
                    ->groupBy('followers.from', 'followers.to')
                    ->pluck('to')
                    ->toArray();
                $users = User::whereIn('id', $followingsIds)
                    ->orderBy('name', 'asc')
                    ->get('id', 'name', 'last_name');
            }

            if ($users->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Herhangi bir danışman bulunamadı.",
                    'consultants' => [],
                ], 404);
            } else {
                // If Users
                foreach ($users as $user) {
                    $user->append('full_name');
                }
                return response([
                    'status' => true,
                    'message' => "Danışmanlar aktarımı başarılı.",
                    'consultants' => $users,
                ], 200);
            }
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }
    public function feeds(Request $request)
    {
        try {
            $page = $request->page;
            $itemsPerPage = $request->itemsPerPage;

            if (authUserInRole(['super-yonetici', 'yonetici'])) {
                $users = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                    ->join('roles', 'role_users.role_id', '=', 'roles.id')
                    ->whereIn('roles.slug', ['ofis-danismani', 'bireysel-danisman'])
                    ->select('users.id', 'users.name', 'users.last_name')
                    ->get();
            }
            if (authUserInRole('ofis-yoneticisi')) {
                $users = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                    ->join('roles', 'role_users.role_id', '=', 'roles.id')
                    ->where('roles.slug', 'ofis-danismani')
                    ->where('users.office_id', auth()->user()->office_id)
                    ->select('users.id', 'users.name', 'users.last_name')
                    ->get();
            }
            if (authUserInRole('ofis-danismani')) {
                $officeConsultantIds = User::join('role_users', 'users.id', '=', 'role_users.user_id')
                    ->join('roles', 'role_users.role_id', '=', 'roles.id')
                    ->where('roles.slug', 'ofis-danismani')
                    ->where('office_id', auth()->user()->office_id)
                    ->where('users.id', '!=', auth()->user()->id)
                    ->select('users.id')
                    ->get()
                    ->pluck('id')
                    ->toArray();
                $followingsIds = Follower::where('from', auth()->user()->id)
                    ->whereNotNull('approved')
                    ->groupBy('followers.from', 'followers.to')
                    ->pluck('to')
                    ->toArray();
                $users = User::whereIn('id', array_unique(array_merge($followingsIds, $officeConsultantIds)))
                    ->orderBy('name', 'asc')
                    ->get('id', 'name', 'last_name');
            }
            if (authUserInRole('bireysel-danisman')) {
                $followingsIds = Follower::where('from', auth()->user()->id)
                    ->whereNotNull('approved')
                    ->groupBy('followers.from', 'followers.to')
                    ->pluck('to')
                    ->toArray();
                $users = User::whereIn('id', $followingsIds)
                    ->orderBy('name', 'asc')
                    ->get('id', 'name', 'last_name');
            }

            if ($users->count() == 0) {
                $feeds = Record::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->paginate($itemsPerPage, ['*'], 'page', $page);
                if ($feeds->count() > 0) {
                    foreach ($feeds as $feed) {
                        if ($feed->portfolio_id) {
                            $portfolio = Portfolio::find($feed->portfolio_id);
                            $portfolioAttachments = $portfolio->attachment()->get()->toArray();
                            if (count($portfolioAttachments) > 0) {
                                $result = array_map(function ($attachment) {
                                    return [
                                        'mime' => $attachment['mime'],
                                        'extension' => $attachment['extension'],
                                        'url' => $attachment['url'],
                                        'name' => $attachment['name'],
                                        'id' => $attachment['id'],
                                    ];
                                }, $portfolioAttachments);
                                $feed->portfolio_first_attachment = $result[0];
                            }
                        }
                        if ($feed->contact_id) {
                            $contact = Contact::find($feed->contact_id);
                            $contactAttachments = $contact->attachment()->get()->toArray();
                            if (count($contactAttachments) > 0) {
                                $result = array_map(function ($attachment) {
                                    return [
                                        'mime' => $attachment['mime'],
                                        'extension' => $attachment['extension'],
                                        'url' => $attachment['url'],
                                        'name' => $attachment['name'],
                                        'id' => $attachment['id'],
                                    ];
                                }, $contactAttachments);
                                $feed->contact_attachment = $result[0];
                            }
                        }
                        $feed->append('record_type_name');
                        $feed->append('user_attachment');
                    }
                    return response([
                        'status' => true,
                        'message' => "Kayıtlarınız aktarıldı.",
                        'feeds' => $feeds
                    ], 200);
                } else {
                    return response([
                        'status' => false,
                        'message' => "Herhangi bir danışman bulunamadı.",
                        'feeds' => []
                    ], 404);
                }
            } else {
                // If Users
                foreach ($users as $user) {
                    $user->append('full_name');
                }
                $userIds = $users->pluck('id');
                $feeds = Record::whereIn('user_id', $userIds)->orWhere('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->paginate($itemsPerPage, ['*'], 'page', $page);
                if ($feeds->count() == 0) {
                    return response([
                        'status' => false,
                        'message' => "Herhangi bir akış kaydı bulunamadı.",
                        'feeds' => []
                    ], 404);
                }
                foreach ($feeds as $feed) {
                    if ($feed->portfolio_id) {
                        $portfolio = Portfolio::find($feed->portfolio_id);
                        $portfolioAttachments = $portfolio->attachment()->get()->toArray();
                        if (count($portfolioAttachments) > 0) {
                            $result = array_map(function ($attachment) {
                                return [
                                    'mime' => $attachment['mime'],
                                    'extension' => $attachment['extension'],
                                    'url' => $attachment['url'],
                                    'name' => $attachment['name'],
                                    'id' => $attachment['id'],
                                ];
                            }, $portfolioAttachments);
                            $feed->portfolio_first_attachment = $result[0];
                        }
                    }
                    if ($feed->contact_id) {
                        $contact = Contact::find($feed->contact_id);
                        $contactAttachments = $contact->attachment()->get()->toArray();
                        if (count($contactAttachments) > 0) {
                            $result = array_map(function ($attachment) {
                                return [
                                    'mime' => $attachment['mime'],
                                    'extension' => $attachment['extension'],
                                    'url' => $attachment['url'],
                                    'name' => $attachment['name'],
                                    'id' => $attachment['id'],
                                ];
                            }, $contactAttachments);
                            $feed->contact_attachment = $result[0];
                        }
                    }
                    $feed->append('record_type_name');
                    $feed->append('user_attachment');
                }
                return response([
                    'status' => true,
                    'message' => "Akış aktarımı başarılı.",
                    'data' => $feeds
                ], 200);
            }
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }
    public function champions(Request $request)
    {
        try {
            // Handle Champions by using request query
            // Get request query
            if ($request->filter) {
                if ($request->filter == 'today') {
                    $championsFilter = 'champsToday';
                } elseif ($request->filter == 'week') {
                    $championsFilter = 'champsWeek';
                }
            } else {
                $championsFilter = 'all';
            }
            // Create A Query of Users depending on records count 
            $query = Record::leftJoin('users', 'users.id', 'records.user_id')
                ->select('user_id', DB::raw('COUNT(*) as record_count'))
                ->groupBy('user_id')
                ->orderByDesc('record_count');

            // Filter Champions by request query
            if ($championsFilter === 'champsToday') {
                // Get records of today (last 24 hours)
                $query->where('records.created_at', '>', now()->subDay());
            } elseif ($championsFilter === 'champsWeek') {
                // Get records of this week(Monday to Sunday)
                $query->whereBetween('records.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            }
            // Get Champions Ids into an array
            $championsIds = $query->limit(5)->pluck('user_id')->toArray();
            // Get Champions User Details
            if (count($championsIds) > 0) {
                $champions = User::whereIn('id', $championsIds)
                    ->orderBy(DB::raw("FIELD(id, " . implode(',', $championsIds) . ")"))
                    ->get(['id', 'name', 'last_name', 'visibility']);
                foreach ($champions as $champion) {
                    $championAttachments = $champion->attachment()->get()->toArray();
                    $result = array_map(function ($attachment) {
                        return [
                            'mime' => $attachment['mime'],
                            'extension' => $attachment['extension'],
                            'url' => $attachment['url'],
                            'name' => $attachment['name'],
                            'id' => $attachment['id'],
                        ];
                    }, $championAttachments);

                    $champion->attachments = $result;
                }
                return response([
                    'status' => true,
                    'message' => "En çok çalışanlar aktarımı başarılı.",
                    'champions' => $champions,
                ], 200);
            } else {
                return response([
                    'status' => false,
                    'message' => "Görünüşe göre kimse çalışmıyor",
                    'champions' => [],
                ], 404);
            }
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function counts(Request $request)
    {
        try {
            // Some Variables
            $authUser = auth()->user();
            // If request has consultant query
            if ($request->filter_consultant) {
                $filteredUser = $request->filter_consultant;
            }
            // Handle Counts by using request query
            // Get request query
            if ($request->filter_date) {
                if ($request->filter_date == 'today') {
                    $countsFilter = 'countsToday';
                } elseif ($request->filter_date == 'week') {
                    $countsFilter = 'countsWeek';
                } elseif ($request->filter_date == 'month') {
                    $countsFilter = 'countsMonth';
                }
            } else {
                $countsFilter = 'all';
            }

            // Handle feed records
            // If user is super-yonetici or yonetici, get all records
            if (authUserInRole(['super-yonetici', 'yonetici'])) {
                $counts = countsOfSystemRecords($filteredUser ?? null, $countsFilter);
            } elseif (authUserInRole(['ofis-yoneticisi', 'ofis-asistani'])) {
                $counts = countsOfOfficeRecords($filteredUser ?? null, $countsFilter);
            } elseif (authUserInRole(['ofis-danismani', 'bireysel-danisman'])) {
                $counts = countsOfUserRecords($filteredUser ?? $authUser->id, $countsFilter);
            }

            return response([
                'status' => true,
                'message' => "Sayıların aktarımı başarılı",
                'counts' => $counts
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function report(Request $request)
    {
        try {
            $report = $request->content;
            $reportName = $report['reportName'];
            $params = [];

            if (isset($report['record']['feed_message'])) {
                $params['consultant_name'] = $report['record']['consultant_name'];
                $params['feed_message'] = $report['record']['feed_message'];
                $params['record_type_name'] = $report['record']['record_type_name'];
            } else {
                $params['consultant_name'] = $report['record']['consultant_name'];
                $params['portfolio_title'] = $report['record']['title'];
            }

            $params['report_name'] = $reportName;

            Mail::send('emails.report', $params, function ($message) {
                $message->to('hello@kodgaraj.com')
                    ->subject('Etiquette - Report');
            });

            return response([
                'status' => true,
                'message' => "Report sent successfully",
            ], 200);
        } catch (\Exception $err) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
                'error' => $err->getMessage() . " " . $err->getLine()
            ], 500);
        }
    }
}
