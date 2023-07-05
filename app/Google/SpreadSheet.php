<?php

namespace App\Google;

use App\Lead;
use App\LeadOrderAssignment;
use Google_Service_Sheets_Spreadsheet;
use Illuminate\Support\Facades\URL;

class SpreadSheet
{
    /**
     * @var string
     */
    protected string $sheetId;

    /**
     * Default role for a user
     *
     * @var string
     */
    public const WRITING = 'writer';

    /**
     * SpreadSheet constructor.
     *
     * @param string $sheetId
     *
     * @return void
     */
    public function __construct($sheetId)
    {
        $this->sheetId = $sheetId;
    }

    /**
     * Grant permissions on spreadsheet
     *
     * @param string $email
     * @param string $access
     *
     * @return \Google_Service_Drive_Permission
     */
    public function grant(string $email, $access = self::WRITING)
    {
        return app('drive')->permissions
            ->create($this->sheetId, new \Google_Service_Drive_Permission([
                'role'                   => $access,
                'type'                   => 'user',
                'emailAddress'           => $email,
            ]), ['sendNotificationEmail'  => false]);
    }

    /**
     * Grant permissions on spreadsheet
     *
     * @param string $email
     *
     * @return \Google_Service_Drive_Permission
     */
    public function transferOwnership(string $email)
    {
        return app('drive')->permissions
            ->create($this->sheetId, new \Google_Service_Drive_Permission([
                'role'                   => 'owner',
                'type'                   => 'user',
                'emailAddress'           => $email,
            ]), ['transferOwnership' => true]);
    }

    /**
     * Create new spreadsheet
     *
     * @param string $name
     *
     * @return \App\Google\SpreadSheet
     */
    public static function create($name): SpreadSheet
    {
        $id = app('sheets')->spreadsheets->create(new Google_Service_Sheets_Spreadsheet([
            'properties' => [
                'title' => $name
            ],
            'sheets' => [new \Google_Service_Sheets_Sheet([
                'properties' => [
                    'title'          => 'Default',
                    'gridProperties' => ['frozenRowCount' => 1]
                ],
            ])]
        ]), [
            'fields' => ['spreadsheetId']
        ])->spreadsheetId;

        return new static($id);
    }

    public function delete()
    {
        app('drive')->files->delete($this->sheetId);
    }
    /**
     * Get spreadsheet sheets
     *
     * @return \Google_Service_Sheets_Sheet
     */
    public function sheets()
    {
        return app('sheets')->spreadsheets->get($this->sheetId)->getSheets();
    }

    /**
     * Determine is sheet already exists
     *
     * @param string $title
     *
     * @return bool
     */
    public function hasSheet(string $title): bool
    {
        return collect($this->sheets())
            ->filter(fn (\Google_Service_Sheets_Sheet $sheet) => $sheet->getProperties()['title'] === $title)
            ->isNotEmpty();
    }

    /**
     * Get raw sheet id
     *
     * @return string
     */
    public function getSheetId(): string
    {
        return $this->sheetId;
    }

    /**
     * Create new sheet
     *
     * @param string $title
     *
     * @return \App\Google\Sheet
     */
    public function createSheet(string $title)
    {
        return Sheet::make($this->sheetId, $title);
    }

    /**
     * Set default spreadsheet headers
     *
     * @param string $sheet
     *
     * @return \Google_Service_Sheets_UpdateValuesResponse
     */
    public function setHeaders(string $sheet)
    {
        return Sheet::setHeaders($this->sheetId, $sheet);
    }

    /**
     * Allow anyone to access sheet
     *
     * @return \Google_Service_Drive_Permission
     */
    public function open()
    {
        return app('drive')->permissions
            ->create($this->sheetId, new \Google_Service_Drive_Permission([
                'role'                   => 'writer',
                'type'                   => 'anyone',
            ]), ['sendNotificationEmail'  => false]);
    }

    /**
     * Append lead data to sheet
     *
     * @param string                   $sheet
     * @param \App\LeadOrderAssignment $assignment
     *
     * @return \Google_Service_Sheets_AppendValuesResponse
     */
    public function appendLead(string $sheet, LeadOrderAssignment $assignment)
    {
        return app('sheets')->spreadsheets_values
            ->append($this->sheetId, sprintf('%s!A1:O1', $sheet), new \Google_Service_Sheets_ValueRange([
                'values' => [
                    [
                        $assignment->lead->id,  // ID
                        $assignment->lead->fullname, // Name
                        $assignment->lead->phone, // Phone
                        $assignment->registered_at->toDateTimeString(), // Registration date
                        now()->toDateTimeString(), // Push datetime
                        '',  // Call datetime,
                        'Новый', // Status
                        '',  // Note
                        '',  // Gender
                        '',  // Profession
                        '',  // Age
                        '',  // Timezone
                        '',  // Deposit amount
                        '',
                        $assignment->lead->hasPoll() ? URL::signedRoute('crm.quiz', ['uuid' => $assignment->lead->uuid]) : '',   // Comment
                        $assignment->lead->email ?? '',
                        $assignment->lead->hasPoll() ? $assignment->lead->getPollAsText() : '',
                    ]
                ],
            ]), ['valueInputOption' => 'RAW']);
    }
}
