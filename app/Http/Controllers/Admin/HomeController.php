<?php

namespace App\Http\Controllers\Admin;

use App\Attendance;
use App\Invoice;

class HomeController
{
    public function index()
    {
        $lastFewMonths = now()->subMonths(5);

        $attendances = Attendance::whereBetween('event_date', [$lastFewMonths->startOfMonth(), now()->endOfMonth()])
            ->selectRaw('DATE_FORMAT(event_date, \'%Y-%m\') AS date')
            ->selectRaw('count(*) as total')
            ->groupBy('date')
            ->get();

        $revenue = [];
        $months  = [];
        foreach ($attendances as $item) {
            $revenue[] = $item['total'] * config('invoices.daily_rate');
            $months[]  = $item['date'];
        }

        $unpaidInvoices = Invoice::with('student')
            ->whereNull('paid_at')
            ->where('period_to', '<=', now()->subMonths(2)->endOfMonth()->toDateString())
            ->get();

        return view('home',compact('revenue', 'months', 'unpaidInvoices'));
    }
}
