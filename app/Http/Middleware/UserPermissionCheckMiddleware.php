<?php

namespace App\Http\Middleware;

use App\Models\Record;
use App\Models\RecordType;
use Closure;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Toast;

class UserPermissionCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->method() == 'GET') {

            if ($request->record == null) {
                return $next($request);
            }

            $url = $request->url();
            $recordTypeMappings = [
                'call' => 'Çağrı',
                'fsbo' => 'F.S.B.O.',
                'viewing' => 'Yer Gösterme',
                'customer' => 'Alıcı Müşteri',
                'marketing' => 'Pazarlama',
                'deed' => 'Tapu Satış-Kiralama İşlemleri',
                'sale' => 'Satış Kapama',
            ];

            $recordTypeId = null;
            foreach ($recordTypeMappings as $keyword => $recordTypeName) {
                if (str_contains($url, $keyword)) {
                    $recordTypeId = RecordType::where('name', $recordTypeName)->value('id');
                    break;
                }
            }
            if (!$recordTypeId) {
                return redirect('/');
            }

            $recordId = $request->record;
            $record = Record::find($recordId);
            if (!$record || $record->record_type_id != $recordTypeId) {
                Toast::error(!$record ? 'Kayıt bulunamadı.' : 'Bilgiler eşleşmiyor');
                return redirect()->route('platform.main');
            }
            if ($record->deleted_at != null || $record->approved_at != null) {

                Toast::error($record->deleted_at ? 'Silinen kayıtlar düzenlenemez' : 'Onaylanan kayıtlar düzenlenemez');
                return redirect()->route('platform.main');
            }

            $user = $request->user();
            if ($user && ($user->inRole('super-yonetici') || $user->inRole('yonetici') || $user->id == $record->user_id || ($user->inRole('ofis-yoneticisi') && $user->office_id == $record->userS->office_id))) {
                return $next($request);
            }

            return redirect('/');
        }

        return $next($request);
    }
}
