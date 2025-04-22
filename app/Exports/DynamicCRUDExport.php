<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Database\Eloquent\Model;

class DynamicCRUDExport implements FromCollection, WithHeadings
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function collection()
    {
        return $this->model->all();
    }

    public function headings(): array
    {
        return array_keys($this->model->first()?->toArray() ?? []);
    }
}
