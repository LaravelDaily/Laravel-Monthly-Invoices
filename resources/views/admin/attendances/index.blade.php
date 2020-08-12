@extends('layouts.admin')
@section('content')
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        <div class="text-center m-3">
            @foreach($paginationLinks as $link)
                @if($link['year'] == $selectedYear && $link['month'] == $selectedMonth)
                    <span class="mr-2 font-weight-bold">
                        {{ $link['fullName'] }}
                    </span>
                @else
                    <a class="mr-2 font-weight-bold" href="{{ route('admin.attendances.index', ['year' => $link['year'], 'month' => $link['month']]) }}">
                        {{ $link['fullName'] }}
                    </a>
                @endif
            @endforeach
        </div>
        <form action="{{ route('admin.attendances.store', ['year' => $selectedYear, 'month' => $selectedMonth]) }}" method="post">
            @csrf
            <div class="table-responsive">
                <table class=" table-sm table-bordered table-striped table-hover datatable">
                    <thead>
                        <tr>
                            <th style="width: 85px">Students/Days</th>
                            @for($i = 1; $i <= $daysInMonth; $i++)
                                <th style="width: 5px">{{ $i }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                @for($i = 1; $i <= $daysInMonth; $i++)
                                    <td style="width: 5px">
                                        <input
                                            type="checkbox"
                                            name="student_{{ $student->id }}[]"
                                            value="{{ $day = now()->setYear($selectedYear)->setMonth($selectedMonth)->setDay($i)->format('Y-m-d') }}"

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
