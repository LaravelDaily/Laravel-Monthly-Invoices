@extends('layouts.admin')
@section('content')
    <div class="card-body">
        <div class="text-center m-3">
            @foreach(\App\Services\AttendanceService::getAttendancePaginationLinks() as $date)
                @if(Route::current()->parameters()['year'] == $date['year'] && Route::current()->parameters()['month'] == $date['month'])
                    <span class="mr-2 font-weight-bold">
                        {{ \App\Services\AttendanceService::getYearAndFullMonthName($date['year'], $date['month']) }}
                    </span>
                @else
                    <a class="mr-2 font-weight-bold" href="{{ route('admin.attendances.index', ['year' => $date['year'], 'month' => $date['month']]) }}">
                        {{ \App\Services\AttendanceService::getYearAndFullMonthName($date['year'], $date['month']) }}
                    </a>
                @endif
            @endforeach
        </div>
        <form action="{{ route('admin.attendances.store', ['year' => Route::current()->parameters()['year'], 'month' => Route::current()->parameters()['month']]) }}" method="post">
            @csrf
            <div class="table-responsive">
                <table class=" table-sm table-bordered table-striped table-hover datatable">
                    <thead>
                        <tr>
                            <th style="width: 85px">Students/Days</th>
                            @for($i = 1; $i <= \App\Services\AttendanceService::daysInMonth(); $i++)
                                <th style="width: 5px">{{ $i }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                @for($i = 1; $i <= \App\Services\AttendanceService::daysInMonth(); $i++)
                                    <td style="width: 5px">
                                        <input
                                            type="checkbox"
                                            name="student_{{ $student->id }}[]"
                                            value="{{ $day = \App\Services\AttendanceService::getYearAndMonth($i) }}"

                                            {{ isset($attendances[$student->id][$day]) ? 'checked' : '' }}
                                        >
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <input class="mt-2" type="submit" value="Save attendance">
        </form>
    </div>
@endsection
