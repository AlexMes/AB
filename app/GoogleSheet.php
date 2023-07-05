<?php

namespace App;

use App\Events\GoogleSheetCreated;
use App\Google\DocMap;
use App\Google\Sheet;
use App\Jobs\Google\SetAgeValidationRule;
use App\Jobs\Google\SetGenderValidationRule;
use App\Jobs\Google\SetNoteValidationRule;
use App\Jobs\Google\SetProfessionValidationRule;
use App\Jobs\Google\SetProtectedRange;
use App\Jobs\Google\SetSheetsHeaders;
use App\Jobs\Google\SetStatusValidationRule;
use App\Jobs\Google\SetTimezoneValidationRule;
use Carbon\Carbon;
use Google_Service_Sheets_SheetProperties;
use Illuminate\Database\Eloquent\Model;

/**
 * App\GoogleSheet
 *
 * @property int $id
 * @property string $title
 * @property string $spreadsheet_id
 * @property int $sheet_id
 * @property int $index
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleSheet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleSheet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleSheet query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleSheet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleSheet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleSheet whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleSheet whereSheetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleSheet whereSpreadsheetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleSheet whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoogleSheet whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GoogleSheet extends Model
{
    protected $guarded = [];

    protected $dispatchesEvents = [
        'created' => GoogleSheetCreated::class,
    ];

    /**
     * @param string                                 $spreadsheetId
     * @param \Google_Service_Sheets_SheetProperties $response
     */
    public static function fromResponse(string $spreadsheetId, Google_Service_Sheets_SheetProperties $response)
    {
        static::create([
            'spreadsheet_id' => $spreadsheetId,
            'sheet_id'       => $response->sheetId,
            'title'          => $response->title,
            'index'          => $response->index
        ]);
    }

    /**
     * Set sheet headers
     *
     * @return void
     */
    public function setupHeaders()
    {
        SetSheetsHeaders::dispatch($this);
    }

    /**
     * Set sheet validation rules
     *
     * @return void
     */
    public function setupValidation()
    {
        SetAgeValidationRule::withChain([
            new SetGenderValidationRule($this),
            new SetNoteValidationRule($this),
            new SetProfessionValidationRule($this),
            new SetStatusValidationRule($this),
            new SetTimezoneValidationRule($this),
        ])->dispatch($this);
    }

    /**
     * Setup headers & validation
     *
     * @return void
     */
    public function configure()
    {
        SetSheetsHeaders::withChain([
            new SetProtectedRange($this),
            new SetAgeValidationRule($this),
            new SetGenderValidationRule($this),
            new SetNoteValidationRule($this),
            new SetProfessionValidationRule($this),
            new SetStatusValidationRule($this),
            new SetTimezoneValidationRule($this),
        ])->dispatch($this);
    }

    /**
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    public function pull()
    {
        $leads = collect(Sheet::pull($this->spreadsheet_id, $this->title));

        return $leads->skip(1)->map(function ($data) {
            try {
                $assignment = LeadOrderAssignment::whereLeadId(digits($data[0] ?? null))
                    ->whereHas('route', fn ($q) => $q->whereManagerId(optional($this->getManager())->id))
                    ->latest('id')
                    ->first();
            } catch (\Throwable $th) {
                // Hard-coded doc causes this shit, just skip and leave it.
                return null;
            }

            if ($assignment === null) {
                return null;
            }

            $assignment->called_at = transform($data[5] ?? null, function ($date) {
                try {
                    return Carbon::parse($date)->toDateTimeString();
                } catch (\Throwable $exception) {
                    return null;
                }
            });
            $assignment->status = DocMap::STATUSES[$data[6] ?? null] ?? null;
            $assignment->reject_reason = DocMap::REASONS[$data[7] ?? null] ?? null;
            $assignment->gender_id = DocMap::GENDERS[$data[8] ?? ''] ?? 0;
            $assignment->profession = DocMap::PROFESSIONS[$data[9] ?? null] ?? null;
            $assignment->age = DocMap::AGES[$data[10] ?? null] ?? null;
            $assignment->timezone = DocMap::TIMEZONES[$data[11] ?? null] ?? null;
            $assignment->deposit_sum = digits($data[12] ?? null);
            $assignment->comment = $data[13] ?? null;

            return $assignment;
        });
    }

    /**
     * Get sheet protected ranges
     *
     * @return mixed
     */
    public function getProtectedRanges()
    {
        $spreadsheet = app('sheets')->spreadsheets->get($this->spreadsheet_id);

        return collect($spreadsheet->getSheets())->flatMap->getProtectedRanges();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|Manager|null
     */
    public function getManager()
    {
        return Manager::where('spreadsheet_id', $this->spreadsheet_id)->first();
    }
}
