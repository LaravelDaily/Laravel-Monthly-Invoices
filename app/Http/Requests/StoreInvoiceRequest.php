<?php

namespace App\Http\Requests;

use App\Invoice;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('invoice_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;

    }

    public function rules()
    {
        return [
            'student_id'     => [
                'required',
                'integer'],
            'period_from'    => [
                'required',
                'date_format:' . config('panel.date_format')],
            'period_to'      => [
                'required',
                'date_format:' . config('panel.date_format')],
            'invoice_number' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647'],
            'total_amount'   => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647'],
            'paid_at'        => [
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'nullable'],
        ];

    }
}
