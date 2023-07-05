<?php

namespace App\Google;

use App\AdsBoard;
use Google_Service_Sheets_Editors;

class ProtectedRange
{
    /**
     * @var int
     */
    protected int $sheetId;

    /**
     * ProtectedRange constructor.
     *
     * @param int $sheetId
     */
    public function __construct(int $sheetId)
    {
        $this->sheetId = $sheetId;
    }

    /**
     * @return \Google_Service_Sheets_AddProtectedRangeRequest
     */
    public function getRequest()
    {
        $request =  new \Google_Service_Sheets_AddProtectedRangeRequest();

        $request->setProtectedRange($this->getProtectedRange());

        return $request;
    }

    /**
     * @return \Google_Service_Sheets_GridRange
     */
    protected function getRange(): \Google_Service_Sheets_GridRange
    {
        $range = new \Google_Service_Sheets_GridRange();
        $range->setSheetId($this->sheetId);
        $range->setStartColumnIndex(0);
        $range->setEndColumnIndex(5);
        $range->setStartRowIndex(1);
        $range->setEndRowIndex(999);

        return $range;
    }

    /**
     * @return \Google_Service_Sheets_ProtectedRange
     */
    protected function getProtectedRange(): \Google_Service_Sheets_ProtectedRange
    {
        $protectedRange = new \Google_Service_Sheets_ProtectedRange();
        $protectedRange->setWarningOnly(false);
        $protectedRange->setDescription('Do not edit this, m`kay?');
        $protectedRange->setRange($this->getRange());
        $protectedRange->setEditors($this->getEditors());

        return $protectedRange;
    }

    /**
     * @return \Google_Service_Sheets_Editors
     */
    protected function getEditors(): Google_Service_Sheets_Editors
    {
        $editors = new \Google_Service_Sheets_Editors();
        $editors->setUsers(AdsBoard::teamEmails());

        return $editors;
    }
}
