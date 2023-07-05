<?php

namespace App\CRM\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeadsExport implements FromCollection, WithHeadings
{
    /**
     * @var $data \App\CRM\LeadOrderAssignment[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
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
                'id'      => $row->id,
                'uuid'    => $row->uuid,
                'offer'   => $row->name,
                'name'    => $row->fullname,
                'email'   => $row->email,
                'phone'   => (mb_substr($row->phone, 0, 1) == 8)
                                    ? substr_replace($row->phone, 7, 0, 1) :
                                    $row->phone,
                'time'        => $row->actual_time !== null ? $row->actual_time->format('H:i') : ' - ',
                'manager'     => $row->manager,
                'status'      => $row->status ?? 'Новый',
                'date'        => $row->created_at->format('M d, H:i:s'),
                'comment'     => $row->comment,
                'utm_content' => $row->utm_content,
                'ip'          => $row->ip,
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'UUID', 'Оффер', 'Имя', 'Почта', 'Номер', 'Время', 'Менеджер', 'Статус', 'Выдан', 'Комментарий', 'Креатив', 'IP'];
    }
}
