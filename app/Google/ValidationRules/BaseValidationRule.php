<?php

namespace App\Google\ValidationRules;

use Google_Service_Sheets_ConditionValue;
use Google_Service_Sheets_SetDataValidationRequest;
use Illuminate\Support\Collection;

abstract class BaseValidationRule
{
    /**
     * Sheet id (not fucking spreadsheet!!!)
     *
     * @var int
     */
    protected int $sheetId;

    /**
     * Instance of set data validation request
     *
     * @var \Google_Service_Sheets_SetDataValidationRequest
     */
    protected Google_Service_Sheets_SetDataValidationRequest $request;

    /**
     * Status constructor.
     *
     * @param int $sheetId
     */
    public function __construct(int $sheetId)
    {
        $this->request = new Google_Service_Sheets_SetDataValidationRequest();
        $this->sheetId = $sheetId;
    }

    /**
     * Get range for validation setup
     *
     * @return array
     */
    protected function getRange(): array
    {
        return [
            'sheetId'          => $this->sheetId,
            'startColumnIndex' => $this->getStartColumnIndex(),
            'endColumnIndex'   => $this->getEndColumnIndex(),
            'startRowIndex'    => 1,
            'endRowIndex'      => 999
        ];
    }

    /**
     * Get validation rule object
     *
     * @return array
     */
    protected function getRule(): array
    {
        return [
            'strict'       => true,
            'showCustomUi' => true,
            'condition'    => $this->getCondition()
        ];
    }

    /**
     * Get prepared validation condition object
     *
     * @return array
     */
    protected function getCondition(): array
    {
        return [
            'type'   => 'ONE_OF_LIST',
            'values' => $this->getFormattedConditionValues(),
        ];
    }

    /**
     * Format and wrap condition values into objects
     *
     * @return array
     */
    protected function getFormattedConditionValues(): array
    {
        return Collection::make($this->getConditionValues())
            ->map(function (string $conditionValue) {
                $cv = new Google_Service_Sheets_ConditionValue();
                $cv->setUserEnteredValue($conditionValue);

                return $cv;
            })->values()->toArray();
    }

    /**
     * Get request instance
     *
     * @return array
     */
    protected function getRequest(): array
    {
        return ['range' => $this->getRange(), 'rule' => $this->getRule()];
    }

    /**
     * Perform request and set validation rules
     *
     * @param $spreadsheetId
     *
     * @return \Google_Service_Sheets_BatchUpdateSpreadsheetResponse
     */
    public function set($spreadsheetId)
    {
        return app('sheets')
            ->spreadsheets
            ->batchUpdate($spreadsheetId, new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                'requests' => [
                    'setDataValidation' => $this->getRequest()
                ],
            ]));
    }

    /**
     * Get array of all possible combinations for selector
     *
     * @return array
     */
    abstract public function getConditionValues(): array;

    /**
     * Get start(real) column index.
     * MUST be same as index of column under validation
     *
     * @return int
     */
    abstract public function getStartColumnIndex(): int;

    /**
     * Get end column index.
     * MUST be +1 of column under validation
     *
     * @return int
     */
    abstract public function getEndColumnIndex(): int;
}
