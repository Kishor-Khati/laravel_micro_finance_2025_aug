@extends('layouts.admin')

@section('title', 'Reports & Export Documentation')
@section('subtitle', 'Learn how to use the reporting and export features')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-2">Reports & Export Documentation</h2>
    
    <div class="space-y-8">
        <!-- Overview Section -->
        <div>
            <h3 class="text-xl font-semibold mb-3 text-gray-700">Overview</h3>
            <p class="text-gray-600 mb-4">
                MicroLendHub provides comprehensive reporting and export functionality to help you analyze your microfinance operations.
                You can generate reports for various aspects of your business and export them in Excel or PDF formats for further analysis or sharing with stakeholders.
            </p>
        </div>
        
        <!-- Available Reports Section -->
        <div>
            <h3 class="text-xl font-semibold mb-3 text-gray-700">Available Reports</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-2">Financial Reports</h4>
                    <p class="text-gray-600 text-sm">Revenue, expenses, and profit analysis with trends and cash flow information.</p>
                </div>
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-2">Member Analytics</h4>
                    <p class="text-gray-600 text-sm">Member demographics, growth trends, and activity analysis.</p>
                </div>
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-2">Loan Portfolio</h4>
                    <p class="text-gray-600 text-sm">Loan performance metrics, portfolio overview, and default risk analysis.</p>
                </div>
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-2">Branch Performance</h4>
                    <p class="text-gray-600 text-sm">Branch comparison, performance metrics, and staff productivity.</p>
                </div>
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-2">Transaction Analysis</h4>
                    <p class="text-gray-600 text-sm">Transaction patterns, trends, and volume analysis.</p>
                </div>
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-2">Executive Summary</h4>
                    <p class="text-gray-600 text-sm">High-level business overview with key performance indicators and growth metrics.</p>
                </div>
            </div>
        </div>
        
        <!-- Export Functionality Section -->
        <div>
            <h3 class="text-xl font-semibold mb-3 text-gray-700">Export Functionality</h3>
            <p class="text-gray-600 mb-4">
                Each report can be exported in Excel or PDF format. Look for the export buttons next to the "View Reports" button on the reports dashboard.
            </p>
            
            <div class="flex items-center space-x-2 mb-4">
                <span class="text-gray-700">Example:</span>
                <button class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-center" disabled>View Reports</button>
                <button class="bg-green-600 text-white px-3 py-2 rounded-lg flex items-center justify-center" disabled>
                    <i class="fas fa-file-excel"></i>
                </button>
                <button class="bg-red-600 text-white px-3 py-2 rounded-lg flex items-center justify-center" disabled>
                    <i class="fas fa-file-pdf"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div>
                    <h4 class="font-medium text-gray-800 mb-2">Excel Export</h4>
                    <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                        <li>Click the green Excel icon <i class="fas fa-file-excel text-green-600"></i> to export data in Excel format</li>
                        <li>Excel exports include all data with proper formatting</li>
                        <li>Useful for further data analysis and manipulation</li>
                        <li>Compatible with Microsoft Excel, Google Sheets, and other spreadsheet applications</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium text-gray-800 mb-2">PDF Export</h4>
                    <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                        <li>Click the red PDF icon <i class="fas fa-file-pdf text-red-600"></i> to export data in PDF format</li>
                        <li>PDF exports include formatted reports with headers and footers</li>
                        <li>Ideal for printing or sharing with stakeholders</li>
                        <li>Professional presentation with consistent formatting</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Technical Implementation Section -->
        <div>
            <h3 class="text-xl font-semibold mb-3 text-gray-700">Technical Implementation</h3>
            <p class="text-gray-600 mb-4">
                For developers, the export functionality is implemented using the following packages:
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-2">Available Export Types</h4>
                    <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                        <li>Members - Export member information and demographics</li>
                        <li>Loans - Export loan details, status, and performance metrics</li>
                        <li>Savings - Export savings account information and balances</li>
                        <li>Transactions - Export transaction history and details</li>
                        <li>Branches - Export branch performance and operational metrics</li>
                        <li>Executive Summary - Export high-level business overview and KPIs</li>
                    </ul>
                </div>
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-2">Excel Export</h4>
                    <p class="text-gray-600 text-sm mb-2">Implemented using <code class="bg-gray-100 px-1 py-0.5 rounded">maatwebsite/excel</code> package.</p>
                    <p class="text-gray-600 text-sm">Export classes are located in <code class="bg-gray-100 px-1 py-0.5 rounded">app/Exports/</code> directory.</p>
                </div>
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-2">PDF Export</h4>
                    <p class="text-gray-600 text-sm mb-2">Implemented using <code class="bg-gray-100 px-1 py-0.5 rounded">barryvdh/laravel-dompdf</code> package.</p>
                    <p class="text-gray-600 text-sm">PDF templates are located in <code class="bg-gray-100 px-1 py-0.5 rounded">resources/views/admin/reports/pdf/</code> directory.</p>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h4 class="font-medium text-gray-800 mb-2">Creating Custom Exports</h4>
                <p class="text-gray-600 text-sm mb-3">To create a new export, follow these steps:</p>
                <ol class="list-decimal list-inside text-gray-600 text-sm space-y-2">
                    <li>
                        <span class="font-medium">Create an Export class:</span>
                        <pre class="bg-gray-100 p-2 rounded mt-1 overflow-x-auto">php artisan make:export YourExportName</pre>
                    </li>
                    <li>
                        <span class="font-medium">Implement required interfaces:</span>
                        <pre class="bg-gray-100 p-2 rounded mt-1 overflow-x-auto">use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class YourExportName implements FromCollection, WithHeadings</pre>
                    </li>
                    <li>
                        <span class="font-medium">Create a PDF template:</span>
                        <p class="mt-1">Create a Blade template in <code class="bg-gray-100 px-1 py-0.5 rounded">resources/views/admin/reports/pdf/</code> directory.</p>
                    </li>
                    <li>
                        <span class="font-medium">Add routes for your exports:</span>
                        <pre class="bg-gray-100 p-2 rounded mt-1 overflow-x-auto">Route::get('/reports/your-report/excel', [ReportsController::class, 'yourReportExcel'])->name('reports.your-report.excel');
Route::get('/reports/your-report/pdf', [ReportsController::class, 'yourReportPdf'])->name('reports.your-report.pdf');</pre>
                    </li>
                </ol>
            </div>
        </div>
        
        <!-- Best Practices Section -->
        <div>
            <h3 class="text-xl font-semibold mb-3 text-gray-700">Best Practices</h3>
            <ul class="list-disc list-inside text-gray-600 space-y-2">
                <li>Use filters to narrow down data before exporting large datasets</li>
                <li>Schedule regular exports of critical reports for backup and analysis</li>
                <li>Consider adding date range filters to your reports for more targeted analysis</li>
                <li>For very large datasets, consider implementing background processing for exports</li>
                <li>Customize PDF templates to match your organization's branding</li>
            </ul>
        </div>
    </div>
</div>
@endsection