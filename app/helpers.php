<?php

use App\Models\Follower;
use App\Models\Portfolio;
use App\Models\Setting;
use App\Models\Province;
use App\Models\Record;
use App\Models\RecordType;
use App\Models\State;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Orchid\Attachment\File;
use phpseclib3\Crypt\AES;

function changeDateFormat($date, $date_format = 0)
{
    if ($date_format == 1) {
        return Carbon::parse($date)->format('d.m.Y');
    }
    return Carbon::parse($date)->format('d.m.Y - H:i');
}


function findProvinceName($id)
{
    if ($id == null) {
        return null;
    }
    return Province::find($id)->name;
}

function findStateName($id)
{
    if ($id == null) {
        return null;
    }
    return State::find($id)->name;
}

function findPorfolioGroupName($groupKey)
{
    $jsonData = json_decode(Setting::first()->config, true);
    $groupList = [];
    foreach (array_keys($jsonData['portfolio_groups']) as $key => $groupName) {
        $groupList[$key + 1] = $groupName;
    }
    return $groupList[$groupKey];
}

function findPortfolioVariationName($groupKey, $variationKey)
{
    $jsonData = json_decode(Setting::first()->config, true);
    $groupList = [];
    $variationList = [];
    foreach (array_keys($jsonData['portfolio_groups']) as $key => $groupName) {
        $groupList[$key + 1] = $groupName;
    }
    $variations = $jsonData['portfolio_groups'][$groupList[$groupKey]];
    foreach ($variations as $key => $value) {
        $variationList[$key + 1] = $value;
    }

    return $variationList[$variationKey];
}

function slugGeneratorForMagicLink($string)
{
    $replacements = [
        'ğ' => 'g',
        'ü' => 'u',
        'ş' => 's',
        'ı' => 'i',
        'i' => 'i',
        'ö' => 'o',
        'ç' => 'c',
        'Ğ' => 'G',
        'Ü' => 'U',
        'Ş' => 'S',
        'İ' => 'I',
        'Ö' => 'O',
        'Ç' => 'C',
    ];

    $convertedString = strtolower(strtr($string, $replacements));
    $convertedString = preg_replace('/[^a-z0-9\-]/', '-', $convertedString);
    return $convertedString;
}

function hasUserPermission($permissions = null)
{
    $user = Auth::user();

    if (is_array($permissions)) {
        return $user->hasAnyAccess($permissions);
    } elseif (is_string($permissions)) {
        return $user->hasAccess($permissions);
    } else {
        return 'Parameters should be Array or String';
    }
}

function checkAccess($permission, $recordUserId = null)
{
    $user = Auth::user();

    if ($user && authUserInRole(['super-yonetici', 'yonetici'])) {
        return true;
    }
    if ($user && authUserInRole('ofis-yoneticisi') && $user->office_id == User::find($recordUserId)->office_id) {
        return true;
    }

    if ($user && $user->hasAccess($permission)) {
        if ($recordUserId !== null && $user->id === $recordUserId) {
            return true;
        }
    }

    return false;
}

function canUserDelete()
{
    $user = Auth::user();

    if ($user && $user->inRole('super-yonetici')) {
        return true;
    }

    return false;
}

function doesUserFollow($followerId, $followingId)
{
    $troughWayRecord = Follower::where('from', $followerId)
        ->where('to', $followingId)
        ->first();

    $oppositeWayFollowRecord = Follower::where('from', $followingId)
        ->where('to', $followerId)
        ->first();

    if (!$troughWayRecord && !$oppositeWayFollowRecord) {
        return 'follow';
    }

    if (!$troughWayRecord && $oppositeWayFollowRecord) {
        if ($oppositeWayFollowRecord->approved == null)
            return 'approve';
        return 'follow-back';
    }

    if ($troughWayRecord && !$oppositeWayFollowRecord) {
        if ($troughWayRecord->approved == null)
            return 'waiting';
        return 'unfollow';
    }

    if ($troughWayRecord && $oppositeWayFollowRecord) {
        if ($oppositeWayFollowRecord->approved == null)
            return 'approve-back';
        if ($troughWayRecord->approved == null)
            return 'waiting-back';
        return 'unfollow';
    }
}

function doesFollow($followerId)
{
    $record = Follower::where('from', $followerId)
        ->where('approved', "!=", null)
        ->first();

    if (!$record) {
        return false;
    }

    return true;
}

function doesUserFollowConsultan($followingId)
{
    $user = Auth::user();

    $record = Follower::where('from', $user->id)
        ->where('to', $followingId)
        ->where('approved', "!=", null)
        ->first();

    if (!$record) {
        return false;
    }

    return true;
}

//TODO : Combine these functions
function countsOfUserRecords($userId, $filter = null)
{
    $query = Record::where('user_id', $userId);
    if ($filter == 'countsToday') {
        $query->whereDate('created_at', now()->today());
    } elseif ($filter == 'countsWeek') {
        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    } elseif ($filter == 'countsMonth') {
        $query->whereMonth('created_at', now()->month);
    }
    $records = $query->get();
    $counts = [
        'call' => $records->where('record_type_id', RecordType::where('name', 'Çağrı')->first()->id)->count(),
        'fsbo' => $records->where('record_type_id', RecordType::where('name', 'F.S.B.O.')->first()->id)->count(),
        'viewing' => $records->where('record_type_id', RecordType::where('name', 'Yer Gösterme')->first()->id)->count(),
        'customer' => $records->where('record_type_id', RecordType::where('name', 'Alıcı Müşteri')->first()->id)->count(),
        'marketing' => $records->where('record_type_id', RecordType::where('name', 'Pazarlama')->first()->id)->count(),
        'deed' => $records->where('record_type_id', RecordType::where('name', 'Tapu Satış-Kiralama İşlemleri')->first()->id)
            ->whereNotNull('approved_at')->count(),
        'sale' => $records->where('record_type_id', RecordType::where('name', 'Satış Kapama')->first()->id)->count(),
    ];
    return $counts;
}

function countsOfSystemRecords($userId, $filter = null)
{
    if ($userId == null) {
        $query = Record::query();
    } else {
        $query = Record::where('user_id', $userId);
    }
    if ($filter == 'countsToday') {
        $query->whereDate('created_at', now()->today());
    } elseif ($filter == 'countsWeek') {
        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    } elseif ($filter == 'countsMonth') {
        $query->whereMonth('created_at', now()->month);
    }
    $records = $query->get();
    $counts = [
        'call' => $records->where('record_type_id', RecordType::where('name', 'Çağrı')->first()->id)->count(),
        'fsbo' => $records->where('record_type_id', RecordType::where('name', 'F.S.B.O.')->first()->id)->count(),
        'viewing' => $records->where('record_type_id', RecordType::where('name', 'Yer Gösterme')->first()->id)->count(),
        'customer' => $records->where('record_type_id', RecordType::where('name', 'Alıcı Müşteri')->first()->id)->count(),
        'marketing' => $records->where('record_type_id', RecordType::where('name', 'Pazarlama')->first()->id)->count(),
        'deed' => $records->where('record_type_id', RecordType::where('name', 'Tapu Satış-Kiralama İşlemleri')->first()->id)
            ->whereNotNull('approved_at')->count(),
        'sale' => $records->where('record_type_id', RecordType::where('name', 'Satış Kapama')->first()->id)->count(),
    ];
    return $counts;
}

function countsOfOfficeRecords($userId, $filter = null)
{
    $user = Auth::user();
    if ($userId == null) {
        $query = Record::join('users', 'users.id', 'records.user_id')
            ->where('users.office_id', $user->office_id)
            ->select('records.*');
    } else {
        $query = Record::join('users', 'users.id', 'records.user_id')
            ->where('users.office_id', $user->office_id)
            ->where('records.user_id', $userId)
            ->select('records.*');
    }

    if ($filter == 'countsToday') {
        $query->whereDate('created_at', now()->today());
    } elseif ($filter == 'countsWeek') {
        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    } elseif ($filter == 'countsMonth') {
        $query->whereMonth('created_at', now()->month);
    }
    $records = $query->get();
    $counts = [
        'call' => $records->where('record_type_id', RecordType::where('name', 'Çağrı')->first()->id)->count(),
        'fsbo' => $records->where('record_type_id', RecordType::where('name', 'F.S.B.O.')->first()->id)->count(),
        'viewing' => $records->where('record_type_id', RecordType::where('name', 'Yer Gösterme')->first()->id)->count(),
        'customer' => $records->where('record_type_id', RecordType::where('name', 'Alıcı Müşteri')->first()->id)->count(),
        'marketing' => $records->where('record_type_id', RecordType::where('name', 'Pazarlama')->first()->id)->count(),
        'deed' => $records->where('record_type_id', RecordType::where('name', 'Tapu Satış-Kiralama İşlemleri')->first()->id)
            ->whereNotNull('approved_at')->count(),
        'sale' => $records->where('record_type_id', RecordType::where('name', 'Satış Kapama')->first()->id)->count(),
    ];
    return $counts;
}

function generateOfficeLogo(string $name)
{
    // Define Turkish Characters
    $replacements = [
        'ğ' => 'g',
        'ü' => 'u',
        'ş' => 's',
        'ı' => 'i',
        'i' => 'i',
        'ö' => 'o',
        'ç' => 'c',
        'Ğ' => 'G',
        'Ü' => 'U',
        'Ş' => 'S',
        'İ' => 'I',
        'Ö' => 'O',
        'Ç' => 'C',
    ];

    // Replace all Turkish characters with English characters
    $officeName = strtolower(strtr($name, $replacements));
    // Create Canvas
    $canvas = imagecreatetruecolor(370, 370);

    // Get Background Image
    $background_image = imagecreatefromjpeg(public_path('images/offices.jpeg'));
    // Copy the background image to the canvas
    imagecopy($canvas, $background_image, 0, 0, 0, 0, 370, 370);
    // Set Text Color
    $text_color = imagecolorallocate($canvas, 255, 255, 255);
    // Set Font Path
    $font_path = public_path('fonts/Play-Bold.ttf');
    // Write Office Name's First Character To Canvas and Set Font Size, Angle, Position and Color
    imagettftext($canvas, 150, 0, 180, 280, $text_color, $font_path, mb_strtoupper($officeName[0]));
    // Set File Path
    $imageFilePath = public_path("images/" . $officeName . ".png");
    // Save Canvas as PNG
    imagepng($canvas, $imageFilePath);
    // Destroy Canvas
    imagedestroy($canvas);
    // Get Extension of Image File
    $extension = pathinfo($imageFilePath, PATHINFO_EXTENSION);
    // Create UploadedFile Object
    $file = new UploadedFile($imageFilePath, $officeName . "." . $extension, "image/png");
    // Create Orchid Attachment Object
    $attachment = (new File($file))->allowDuplicates()->load();
    // Destroy First Image File
    unlink($imageFilePath);

    // Return Orchid Attachment Object
    return $attachment;
}

function authUserInRole($slug)
{
    $user = Auth::user();

    if (is_array($slug)) {
        foreach ($slug as $slug) {
            if ($user->inRole($slug)) {
                return true;
            }
        }
    } elseif (is_string($slug)) {
        return $user->inRole($slug);
    } else {
        return 'Parameters should be Array or String';
    }
    return false;
}
function canAuthUserClickPortfolioRecordCounts($portfolioId)
{
    $user = auth()->user();
    if (authUserInRole(['super-yonetici', 'yonetici'])) {
        return true;
    }

    if (authUserInRole('ofis-yoneticisi') && Portfolio::find($portfolioId)->userS->office_id == $user->office_id) {
        return true;
    }

    if (authUserInRole(['ofis-danismani', 'bireysel-danisman']) && Portfolio::find($portfolioId)->user_id == $user->id) {
        return true;
    }

    return false;
}

function authUserCanSelectConsultantForRecord()
{
    return !authUserInRole(['bireysel-danisman', 'ofis-danismani']);
}

function encryptText($text)
{
    // Şifreleme modunu ve AES nesnesini oluşturun
    $aes = new AES('gcm');

    // Şifreleme anahtarını ayarlayın
    $aes->setKey(env('API_SECRET'));

    // Rastgele bir Nonce (12 byte uzunluğunda) oluşturun
    $nonce = random_bytes(12);
    $aes->setNonce($nonce);

    // Metni şifreleyin
    $encryptedText = $aes->encrypt($text);

    // Authentication Tag'i alın
    $tag = $aes->getTag();

    return [
        'encryptedText' => base64_encode($encryptedText),
        'nonce' => base64_encode($nonce),
        'tag' => base64_encode($tag),
    ];
}

function decryptText($encryptedText, $nonce, $tag)
{
    // Şifreleme modunu ve AES nesnesini oluşturun
    $aes = new AES('gcm');

    // Şifreleme anahtarını ayarlayın
    $aes->setKey(env('API_SECRET'));

    // Nonce'u ayarlayın
    $aes->setNonce(base64_decode($nonce));

    // Authentication Tag'i ayarlayın
    $aes->setTag(base64_decode($tag));

    // Metni çözün
    $decryptedText = $aes->decrypt(base64_decode($encryptedText));

    return $decryptedText;
}
