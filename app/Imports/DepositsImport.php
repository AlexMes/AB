<?php

namespace App\Imports;

use App\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DepositsImport implements ToCollection, WithHeadingRow
{
    use Importable;

    protected $user;

    protected $rows;

    /**
     * DepositsImport constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @inheritDoc
     */
    public function collection(Collection $collection)
    {
        $this->rows = $collection;
    }

    public function get()
    {
        return $this->rows;
    }
}
