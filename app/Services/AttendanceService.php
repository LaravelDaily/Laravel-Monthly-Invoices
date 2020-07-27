<?php

namespace App\Services;

use App\Student;
use App\Attendance;
use Illuminate\Support\Facades\Route;

/**
 * Class AttendanceService
 * @package App\Services
 */
class AttendanceService
{
    /**
     * @return mixed
     */
    public function getAttendances()
    {
        return Attendance::select(['student_id', 'event_date'])
            ->get()
            ->groupBy('student_id')
            ->map(function ($items) {
                return $items->pluck('student_id', 'event_date');
            });
    }

    /**
     * @param int $year
     * @param int $month
     * @param array $attendances
     */
    public function storeAttendances(int $year, int $month, array $attendances)
    {
        Attendance::query()
            ->whereYear('event_date', $year)
            ->whereMonth('event_date', $month)
            ->delete();

        foreach ($attendances as $key => $dates) {
            list(, $studentId) = explode('_', $key);
            $student = Student::find($studentId);

            $studentAttendances = [];
            foreach ($dates as $date) {
                $studentAttendances[] = new Attendance(['event_date' => $date]);
            }

            $student->attendances()->saveMany($studentAttendances);
        }
    }

    /**
     * @return int
     */
    public static function daysInMonth()
    {
        return now()->setYear(Route::current()->parameters()['year'])
            ->setMonth(Route::current()->parameters()['month'])
            ->daysInMonth;
    }

    /**
     * @return array
     */
    public static function getAttendancePaginationLinks()
    {
        $routeYear   = Route::current()->parameters()['year'];
        $routeMonth  = Route::current()->parameters()['month'];
        $currentDate = now()->setYear($routeYear)->setMonth($routeMonth)->toImmutable();

        $dates = [
            [
                'year'  => $currentDate->subMonth()->year,
                'month' => $currentDate->subMonth()->format('m'),
            ],
            [
                'year'  => $currentDate->year,
                'month' => $currentDate->format('m'),
            ],
        ];

        if ($routeYear < now()->year | ($routeYear == now()->year && $routeMonth < now()->month)) {
            $dates[] =
                [
                    'year'  => $currentDate->addMonth()->year,
                    'month' => $currentDate->addMonth()->format('m'),
                ];
        }

        return $dates;
    }

    /**
     * @return bool
     */
    public static function isAttendanceDateGreaterThanCurrentMonth()
    {
        $routeYear  = Route::current()->parameters()['year'];
        $routeMonth = Route::current()->parameters()['month'];

        if ($routeYear > now()->year) {
            return true;
        }

        if ($routeYear == now()->year && $routeMonth > now()->month) {
            return true;
        }

        return false;
    }

    /**
     * @param $year
     * @param $month
     * @return string
     */
    public static function getYearAndFullMonthName($year, $month)
    {
        return now()->setYear($year)->setMonth($month)->format('F Y');
    }

    /**
     * @param $day
     * @return string
     */
    public static function getYearAndMonth($day)
    {
        $routeYear  = Route::current()->parameters()['year'];
        $routeMonth = Route::current()->parameters()['month'];

        return now()->setYear($routeYear)->setMonth($routeMonth)->setDay($day)->format('Y-m-d');
    }
}
