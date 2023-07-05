<?php

namespace App\Google;

use App\AdsBoard;
use App\Google\ValidationRules\Age;
use App\Google\ValidationRules\Gender;
use App\Google\ValidationRules\Note;
use App\Google\ValidationRules\Profession;
use App\Google\ValidationRules\Status;
use App\Google\ValidationRules\Timezone;
use App\GoogleSheet;

class Sheet
{
    /**
     * @var string
     */
    private string $title;

    /**
     * @var int
     */
    protected int $sheetId;

    /**
     * Validation rules to apply to the sheet
     *
     * @var array
     */
    protected static array $validationRules = [
        Age::class,
        Gender::class,
        Note::class,
        Profession::class,
        Status::class,
        Timezone::class
    ];

    /**
     * Sheet constructor.
     *
     * @param $properties
     *
     * @return void
     */
    public function __construct($properties)
    {
        $this->title      = $properties->title;
        $this->sheetId    = $properties->sheetId;
    }

    /**
     * Make new sheet on specific spreadsheet file
     *
     * @param $spreadsheetId
     * @param $title
     *
     * @return static
     */
    public static function make($spreadsheetId, $title)
    {
        $response = app('sheets')->spreadsheets
            ->batchUpdate($spreadsheetId, new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                'requests' => [
                    'addSheet' => [
                        'properties' => ['title' => $title, 'gridProperties' => ['frozenRowCount' => 1]]
                    ]
                ],
            ]));

        $addSheetResponse = $response->getReplies()[0]->getAddSheet()->getProperties();

        GoogleSheet::fromResponse($spreadsheetId, $addSheetResponse);

        return new static($addSheetResponse);
    }


    /**
     * Set headers on sheet
     *
     * @param $spreadsheetId
     * @param string $sheet
     *
     * @return \Google_Service_Sheets_UpdateValuesResponse
     */
    public static function setHeaders($spreadsheetId, string $sheet)
    {
        return app('sheets')->spreadsheets_values
            ->update($spreadsheetId, sprintf('%s!A1:N1', $sheet), new \Google_Service_Sheets_ValueRange([
                'values' => [
                    [
                        'ID',
                        'Имя',
                        'Номер',
                        'Дата регистрации',
                        'Дата назначения',
                        'Дата прозвона',
                        'Статус',
                        'Примечание',
                        'Пол',
                        'Профессия',
                        'Возраст',
                        'Время',
                        'Сумма депозита',
                        'Комментарий',
                    ]
                ],
            ]), ['valueInputOption' => 'RAW']);
    }


    /**
     * @param string $spreadsheetId
     * @param int    $sheetId
     */
    public static function setValidationRules(string $spreadsheetId, int $sheetId)
    {
        foreach (self::$validationRules as $rule) {
            $rule->applyTo($sheetId);
        }
    }

    /**
     * Set protected range on the sheet
     *
     * @param string $spreadsheetId
     * @param int    $sheetId
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     *
     * @return \Google_Service_Sheets_BatchUpdateSpreadsheetResponse
     */
    public static function setProtectedRange(string $spreadsheetId, int $sheetId)
    {
        return app('sheets')->spreadsheets
            ->batchUpdate($spreadsheetId, new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                'requests' => [
                    'addProtectedRange' => [
                        'protectedRange' => [
                            'protectedRangeId' => rand(1, 500),
                            'range'            => [
                                'sheetId'          => $sheetId,
                                'startRowIndex'    => 0,
                                'endRowIndex'      => 999,
                                'startColumnIndex' => 0,
                                'endColumnIndex'   => 1
                            ],
                            'description' => 'protection',
                            'warningOnly' => false,
                            'editors'     => [
                                'users' => [
                                    AdsBoard::teamEmails(),
                                ]
                            ]
                        ]
                    ],
                ],
            ]));
    }

    /**
     * Pull and collect sheet values
     *
     * @param string $spreadsheetId
     * @param string $title
     *
     * @return \Google_Service_Sheets_ValueRange
     */
    public static function pull(string $spreadsheetId, string $title)
    {
        return app('sheets')->spreadsheets_values->get($spreadsheetId, sprintf('%s!A:N', $title));
    }
}
