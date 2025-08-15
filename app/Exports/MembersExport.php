<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MembersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Member::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Member ID',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Address',
            'City',
            'State',
            'Postal Code',
            'Country',
            'Date of Birth',
            'Gender',
            'Occupation',
            'Branch',
            'Joined Date',
            'Status',
        ];
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->member_id,
            $row->first_name,
            $row->last_name,
            $row->email,
            $row->phone,
            $row->address,
            $row->city,
            $row->state,
            $row->postal_code,
            $row->country,
            $row->date_of_birth ? $row->date_of_birth->format('Y-m-d') : '',
            $row->gender,
            $row->occupation,
            $row->branch ? $row->branch->name : '',
            $row->created_at->format('Y-m-d'),
            $row->status,
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}