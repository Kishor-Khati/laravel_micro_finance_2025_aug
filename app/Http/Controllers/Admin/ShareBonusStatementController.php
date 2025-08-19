<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShareBonus;
use App\Models\Branch;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShareBonusStatementController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::all();
        $members = Member::all();
        
        $query = ShareBonus::with(['branch', 'recipient', 'creator', 'approver'])
            ->where('status', 'approved');
        
        // Filter by branch if specified
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        
        // Filter by recipient if specified
        if ($request->filled('recipient_id')) {
            $query->where('recipient_id', $request->recipient_id);
        }
        
        // Filter by date range if specified
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        
        $shareBonuses = $query->orderBy('date', 'desc')->paginate(20);
        
        // Calculate totals
        $totalAmount = $query->sum('amount');
        
        return view('admin.share-bonus-statement.index', compact(
            'shareBonuses',
            'branches',
            'members',
            'totalAmount'
        ));
    }
    
    public function print(Request $request)
    {
        $branches = Branch::all();
        $members = Member::all();
        
        $query = ShareBonus::with(['branch', 'recipient', 'creator', 'approver'])
            ->where('status', 'approved');
        
        // Apply same filters as index
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        
        if ($request->filled('recipient_id')) {
            $query->where('recipient_id', $request->recipient_id);
        }
        
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        
        $shareBonuses = $query->orderBy('date', 'desc')->get();
        
        // Calculate totals
        $totalAmount = $shareBonuses->sum('amount');
        
        return view('admin.share-bonus-statement.print', compact(
            'shareBonuses',
            'branches',
            'members',
            'totalAmount'
        ));
    }
}