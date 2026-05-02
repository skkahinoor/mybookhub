<?php

namespace App\Exports;

use App\Models\Author;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AuthorsExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Author::select('id', 'name', 'status', 'created_at')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function($author) {
                return [
                    'id' => $author->id,
                    'name' => $author->name,
                    'status' => $author->status == 1 ? 'Active' : 'Inactive',
                    'created_at' => date('F j, Y, g:i a', strtotime($author->created_at)),
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
