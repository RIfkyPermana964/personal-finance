<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $periodType = $request->input('period_type', 'monthly');
        $year = (int) $request->input('year', Carbon::now()->year);
        $month = (int) $request->input('month', Carbon::now()->month);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $report = $this->reportService->getReportData($userId, $periodType, $year, $month, $startDate, $endDate);

        return view('reports.index', compact('report', 'periodType', 'year', 'month', 'startDate', 'endDate'));
    }
}