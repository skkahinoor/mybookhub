<?php

namespace App\Exports;

use App\Models\Publisher;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PublishersExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Publisher::select('id', 'name', 'status', 'created_at')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function($publisher) {
                return [
                    'id' => $publisher->id,
                    'name' => $publisher->name,
                    'status' => $publisher->status == 1 ? 'Active' : 'Inactive',
                    'created_at' => date('F j, Y, g:i a', strtotime($publisher->created_at)),
                ];
            });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Status',
            'Created At',
        ];
    }
}
