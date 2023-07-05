<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportModel implements FromCollection, ShouldAutoSize
{
    use Exportable;

    private $models;

    public function __construct(Collection $models)
    {
        $this->models = $models;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->models;
    }
}
