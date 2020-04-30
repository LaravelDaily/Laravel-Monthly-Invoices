@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.invoice.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.invoices.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="student_id">{{ trans('cruds.invoice.fields.student') }}</label>
                <select class="form-control select2 {{ $errors->has('student') ? 'is-invalid' : '' }}" name="student_id" id="student_id" required>
                    @foreach($students as $id => $student)
                        <option value="{{ $id }}" {{ old('student_id') == $id ? 'selected' : '' }}>{{ $student }}</option>
                    @endforeach
                </select>
                @if($errors->has('student'))
                    <div class="invalid-feedback">
                        {{ $errors->first('student') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.invoice.fields.student_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="period_from">{{ trans('cruds.invoice.fields.period_from') }}</label>
                <input class="form-control date {{ $errors->has('period_from') ? 'is-invalid' : '' }}" type="text" name="period_from" id="period_from" value="{{ old('period_from') }}" required>
                @if($errors->has('period_from'))
                    <div class="invalid-feedback">
                        {{ $errors->first('period_from') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.invoice.fields.period_from_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="period_to">{{ trans('cruds.invoice.fields.period_to') }}</label>
                <input class="form-control date {{ $errors->has('period_to') ? 'is-invalid' : '' }}" type="text" name="period_to" id="period_to" value="{{ old('period_to') }}" required>
                @if($errors->has('period_to'))
                    <div class="invalid-feedback">
                        {{ $errors->first('period_to') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.invoice.fields.period_to_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="invoice_number">{{ trans('cruds.invoice.fields.invoice_number') }}</label>
                <input class="form-control {{ $errors->has('invoice_number') ? 'is-invalid' : '' }}" type="number" name="invoice_number" id="invoice_number" value="{{ old('invoice_number', '') }}" step="1" required>
                @if($errors->has('invoice_number'))
                    <div class="invalid-feedback">
                        {{ $errors->first('invoice_number') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.invoice.fields.invoice_number_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="total_amount">{{ trans('cruds.invoice.fields.total_amount') }}</label>
                <input class="form-control {{ $errors->has('total_amount') ? 'is-invalid' : '' }}" type="number" name="total_amount" id="total_amount" value="{{ old('total_amount', '') }}" step="1" required>
                @if($errors->has('total_amount'))
                    <div class="invalid-feedback">
                        {{ $errors->first('total_amount') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.invoice.fields.total_amount_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection