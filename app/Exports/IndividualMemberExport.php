<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class IndividualMemberExport implements WithMultipleSheets
{
    protected $member;

    public function __construct(Member $member)
    {
        $this->member = $member;
    }

    public function sheets(): array
    {
        return [
            'Member Details' => new MemberDetailsSheet($this->member),
            'Loans' => new MemberLoansSheet($this->member),
            'Savings Accounts' => new MemberSavingsSheet($this->member),
            'Transactions' => new MemberTransactionsSheet($this->member),
        ];
    }
}

class MemberDetailsSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $member;

    public function __construct(Member $member)
    {
        $this->member = $member;
    }

    public function collection()
    {
        return collect([$this->member]);
    }

    public function headings(): array
    {
        return [
            'Member ID',
            'First Name',
            'Middle Name',
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
            'Citizenship Number',
            'Branch',
            'Joined Date',
            'Status',
            'KYC Status',
        ];
    }

    public function map($member): array
    {
        return [
            $member->member_id,
            $member->first_name,
            $member->middle_name,
            $member->last_name,
            $member->email,
            $member->phone,
            $member->address,
            $member->city,
            $member->state,
            $member->postal_code,
            $member->country,
            $member->date_of_birth ? $member->date_of_birth->format('Y-m-d') : '',
            $member->gender,
            $member->occupation,
            $member->citizenship_number,
            $member->branch ? $member->branch->name : '',
            $member->created_at->format('Y-m-d'),
            $member->status,
            $member->kyc_status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class MemberLoansSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $member;

    public function __construct(Member $member)
    {
        $this->member = $member;
    }

    public function collection()
    {
        return $this->member->loans;
    }

    public function headings(): array
    {
        return [
            'Loan Number',
            'Loan Type',
            'Principal Amount',
            'Interest Rate (%)',
            'Term (Months)',
            'Monthly Payment',
            'Total Payable',
            'Total Paid',
            'Remaining Balance',
            'Start Date',
            'End Date',
            'Status',
            'Created Date',
        ];
    }

    public function map($loan): array
    {
        return [
            $loan->loan_number,
            $loan->loanType ? $loan->loanType->name : '',
            number_format($loan->principal_amount, 2),
            $loan->interest_rate,
            $loan->term_months,
            number_format($loan->monthly_payment, 2),
            number_format($loan->total_payable, 2),
            number_format($loan->total_paid, 2),
            number_format($loan->remaining_balance, 2),
            $loan->start_date ? $loan->start_date->format('Y-m-d') : '',
            $loan->end_date ? $loan->end_date->format('Y-m-d') : '',
            $loan->status,
            $loan->created_at->format('Y-m-d'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class MemberSavingsSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $member;

    public function __construct(Member $member)
    {
        $this->member = $member;
    }

    public function collection()
    {
        return $this->member->savingsAccounts;
    }

    public function headings(): array
    {
        return [
            'Account Number',
            'Savings Type',
            'Balance',
            'Interest Rate (%)',
            'Minimum Balance',
            'Status',
            'Opened Date',
        ];
    }

    public function map($account): array
    {
        return [
            $account->account_number,
            $account->savingsType ? $account->savingsType->name : '',
            number_format($account->balance, 2),
            $account->interest_rate,
            number_format($account->minimum_balance, 2),
            $account->status,
            $account->created_at->format('Y-m-d'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class MemberTransactionsSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $member;

    public function __construct(Member $member)
    {
        $this->member = $member;
    }

    public function collection()
    {
        return $this->member->transactions()->latest()->take(100)->get();
    }

    public function headings(): array
    {
        return [
            'Transaction ID',
            'Type',
            'Amount',
            'Description',
            'Reference Type',
            'Reference ID',
            'Balance Before',
            'Balance After',
            'Date',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->transaction_id,
            $transaction->transaction_type,
            number_format($transaction->amount, 2),
            $transaction->description,
            $transaction->reference_type,
            $transaction->reference_id,
            number_format($transaction->balance_before, 2),
            number_format($transaction->balance_after, 2),
            $transaction->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}