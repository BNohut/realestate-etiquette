<?php

namespace App\Models;

use App\Presenters\BladePresenter;
use App\Traits\UserPermissionTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Orchid\Filters\Filterable;
use Laravel\Scout\Searchable;
use Orchid\Screen\AsSource;

class Record extends Model
{
    use HasFactory, Filterable, Searchable, AsSource, SoftDeletes, UserPermissionTrait;

    protected $table = 'records';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'contact_id',
        'portfolio_id',
        'province_id',
        'state_id',
        'neighborhood',
        'record_type_id',
        'portfolio_group',
        'portfolio_variation',
        'contact_resource',
        'portfolio_type',
        'property_features',
        'notes',
        'activity_type',
        'record_date',
        'link',
        'prepayment',
        'sales_price',
        'record_result',
        'record_level',
        'budget',
        'price_offer',
        'feed_message',
        "likes"
    ];

    protected $appends = [
        'contact_name',
        'consultant_name',
        'portfolio_title',
    ];

    public function toSearchableArray()
    {
        $array = [];
        $array['id'] = $this->id;
        if (isset($this->portfolio_id)) {
            $array['portfolio_title'] = $this->portfolioS->title;
            $array['province'] = $this->portfolioS->provinceS->name;
        } else {
            $array['province'] = $this->provinceS->name;
        }
        $array['record_type'] = $this->recordTypeS->name;
        $array['consultant_name'] = $this->consultant_name;
        $array['contact_name'] = $this->contact_name;
        $array['notes'] = $this->notes;
        $array['facet'] = "Kayıtlar";

        return $array;
    }

    public function getOfficeNameAttribute()
    {
        $user = User::find($this->user_id);
        $officeId = $user->office_id;
        if ($officeId == null) return null;

        $office = Office::find($officeId);

        return $office->name;
    }

    public function getRecordTypeNameAttribute()
    {
        if ($this->record_type_id == null) return null;

        $recordType = RecordType::find($this->record_type_id);

        return $recordType->name;
    }

    public function getPortfolioListPriceAttribute()
    {
        if ($this->portfolio_id == null) return null;

        $portfolio = Portfolio::find($this->portfolio_id);

        return $portfolio->list_price;
    }

    public function getPortfolioTitleAttribute()
    {
        if ($this->portfolio_id == null) return null;

        $portfolio = Portfolio::find($this->portfolio_id);

        return $portfolio->title;
    }

    public function getConsultantNameAttribute()
    {
        if ($this->user_id == null) return null;

        $user = User::find($this->user_id);

        return $user->name . ' ' . $user->last_name;
    }

    public function getContactNameAttribute()
    {
        if ($this->contact_id == null) return null;

        $contact = Contact::find($this->contact_id);

        return $contact->name;
    }

    public function getPortfolioAttachmentsAttribute()
    {
        if ($this->portfolio_id == null) return null;

        $portfolio = Portfolio::find($this->portfolio_id);
        $marketingPortfolioAttachments = $portfolio->attachment()->get()->toArray();
        $result = array_map(function ($attachment) {
            return [
                'mime' => $attachment['mime'],
                'extension' => $attachment['extension'],
                'url' => $attachment['url'],
                'name' => $attachment['name'],
                'id' => $attachment['id'],
            ];
        }, $marketingPortfolioAttachments);
        return $result;
    }

    public function getUserAttachmentAttribute()
    {
        $user = User::find($this->user_id);
        $userAttachment = $user->attachment()->get()->toArray();
        if (!$userAttachment) return [];

        $result = array_map(function ($attachment) {
            return [
                'mime' => $attachment['mime'],
                'extension' => $attachment['extension'],
                'url' => $attachment['url'],
                'name' => $attachment['name'],
                'id' => $attachment['id'],
            ];
        }, $userAttachment);
        return $result;
    }

    public function userS(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function contactS(): HasOne
    {
        return $this->hasOne(Contact::class, 'id', 'contact_id');
    }
    public function portfolioS(): HasOne
    {
        return $this->hasOne(Portfolio::class, 'id', 'portfolio_id');
    }
    public function provinceS(): HasOne
    {
        return $this->hasOne(Province::class, 'id', 'province_id');
    }
    public function stateS(): HasOne
    {
        return $this->hasOne(State::class, 'id', 'state_id');
    }
    public function recordTypeS(): HasOne
    {
        return $this->hasOne(RecordType::class, 'id', 'record_type_id');
    }

    public function presenter(): BladePresenter
    {
        return new BladePresenter($this);
    }

    // Saving Feed Message while new record creating
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $jsonData = json_decode(Setting::first()->config, true);
            $text = '';
            // FSBO & Customer Records Dont Have Portfolio_Id Fields
            // That's why we need to generate text for feed message for 2 scnarios
            if ($model->recordTypeS->name == "F.S.B.O." || $model->recordTypeS->name == "Alıcı Müşteri") {
                $text = $model->generateRecordTextForNoPortfolioRecords($jsonData);
            } else if ($model->recordTypeS->name == "Portföy") {
                $text = $model->generateRecordTextForPortfolios();
            } else {
                $text = $model->generateRecordTextForPortfolioRecords();
            }

            $model->feed_message = $text;
        });
    }


    // Scenario 1: Text for FSBO & Customer Records
    protected function generateRecordTextForNoPortfolioRecords($jsonData)
    {
        $groupList = $jsonData['portfolio_groups'];
        $newList = [];
        foreach ($groupList as $key => $value) {
            $newList[$key] = $key;
        }
        $variationList = $jsonData['portfolio_groups'][$this->portfolio_group];

        $text = $this->neighborhood . " / " .
            $this->stateS->name . " / " .
            $this->provinceS->name . " adresinde " .
            $newList[$this->portfolio_group] . " / " .
            $variationList[$this->portfolio_variation] . " için " .
            $this->recordTypeS->name . " kaydı ekledi.";

        return $text;
    }
    // Scenario 2: Text For Other Records
    protected function generateRecordTextForPortfolioRecords()
    {
        $text = $this->portfolioS->neighborhood . " / " .
            $this->portfolioS->stateS->name . " / " .
            $this->portfolioS->provinceS->name . " adresinde " .
            $this->portfolioS->title . " başlıklı portföy için " .
            $this->recordTypeS->name . " kaydı ekledi.";
        return $text;
    }

    // Scenario 3: Text For Portfolio Records
    protected function generateRecordTextForPortfolios()
    {
        $text = $this->portfolioS->neighborhood . " / " .
            $this->portfolioS->stateS->name . " / " .
            $this->portfolioS->provinceS->name . " adresinde " .
            $this->portfolioS->title . " başlıklı portföy ekledi.";
        return $text;
    }
}
