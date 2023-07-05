<?php

namespace App\Http\Controllers\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use phpDocumentor\Reflection\Types\Collection;

class LeadsExport implements FromCollection, WithHeadings
{
    /**
     * @var Collection
     */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->data->map(function ($row) {
            return [
                'id'    => $row->id,
                'name'  => $row->fullname,
                'email' => $row->email,
                'phone' => (mb_substr($row->phone, 0, 1) == 8)
                                    ? substr_replace($row->phone, 7, 0, 1) :
                                    $row->phone,
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'Имя', 'Почта', 'Номер'];
    }
}
