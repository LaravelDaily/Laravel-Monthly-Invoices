<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Route;
use Illuminate\View\View;
use App\Services\AttendanceService;
use App\Http\Controllers\Controller;
use App\Student;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AttendanceController
 * @package App\Http\Controllers\Admin
 */
class AttendanceController extends Controller
{
    /**
     * @var AttendanceService
     */
    private $attendanceService;

    /**
     * AttendanceController constructor.
     * @param AttendanceService $attendanceService
     */
    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * @return Factory|RedirectResponse|View
     */
    public function index()
    {
        abort_if(Gate::denies('attendance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $selectedYear  = Route::current()->parameters()['year'];
        $selectedMonth = Route::current()->parameters()['month'];

        //don't let to navigate to future attendances
        if ($this->attendanceService->isProvidedMonthGreaterThanCurrentMonth($selectedYear, $selectedMonth)) {
            return redirect()->route('admin.attendances.redirect');
        }

        $daysInMonth     = $this->attendanceService->daysInMonth($selectedYear, $selectedMonth);
        $students        = Student::all();
        $attendances     = $this->attendanceService->getAttendances();
        $paginationLinks = $this->attendanceService->getAttendancePaginationLinks($selectedYear, $selectedMonth);

        return view('admin.attendances.index', compact(
            'attendances', 'students', 'paginationLinks', 'daysInMonth', 'selectedYear', 'selectedMonth'
        ));
    }

    /**
     * @return RedirectResponse
     */
    public function store()
    {
        $year  = request()->segment(3);
        $month = request()->segment(4);

        $attendances = array_filter(request()->all(), function ($key) {
            return strpos($key, 'student_') === 0;
        }, ARRAY_FILTER_USE_KEY);

        $this->attendanceService->storeAttendances($year, $month, $attendances);

        return redirect()->route('admin.attendances.index', [
            'year'  => $year,
            'month' => $month,
        ])->with('success', 'Attendances updated successfully.');
    }
}
