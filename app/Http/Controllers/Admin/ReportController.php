<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MonthlyReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected MonthlyReportService $reportService
    ) {}

    /**
     * Unduh Laporan Bulanan Resmi dalam format PDF.
     */
    public function downloadPdf(Request $request)
    {
        $year = (int) $request->query('year', date('Y'));
        $month = (int) $request->query('month', date('n'));

        // Batasi rentang tahun dan bulan valid
        if ($year < 2020 || $year > 2050 || $month < 1 || $month > 12) {
            abort(400, 'Parameter tahun atau bulan tidak valid.');
        }

        return $this->reportService->exportPdf($year, $month);
    }
}
