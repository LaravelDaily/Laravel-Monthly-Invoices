<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyInvoiceRequest;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Invoice;
use App\Notifications\MonthlyMemberNotification;
use App\Student;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;

class InvoicesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('invoice_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $invoices = Invoice::all();

        return view('admin.invoices.index', compact('invoices'));
    }

    public function create()
    {
        abort_if(Gate::denies('invoice_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $students = Student::all()->pluck('first_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.invoices.create', compact('students'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $invoice = Invoice::create($request->all());

        return redirect()->route('admin.invoices.index');

    }

    public function edit(Invoice $invoice)
    {
        abort_if(Gate::denies('invoice_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $students = Student::all()->pluck('first_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $invoice->load('student');

        return view('admin.invoices.edit', compact('students', 'invoice'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $invoice->update($request->all());

        return redirect()->route('admin.invoices.index');

    }

    public function show(Invoice $invoice)
    {
        abort_if(Gate::denies('invoice_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $invoice->load('student');

        return view('admin.invoices.show', compact('invoice'));
    }

    public function destroy(Invoice $invoice)
    {
        abort_if(Gate::denies('invoice_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $invoice->delete();

        return back();

    }

    public function massDestroy(MassDestroyInvoiceRequest $request)
    {
        Invoice::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);

    }

    public function togglePaid(Invoice $invoice)
    {
        abort_if(Gate::denies('invoice_toggle_paid'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $paidAt = ($invoice->paid_at == null) ? now() : null;

        $invoice->paid_at = $paidAt;
        $invoice->save();

        return redirect()->back();
    }

    public function resend(Invoice $invoice)
    {
        $student = $invoice->student;

        Notification::route('mail', $student->email)->notify(new MonthlyMemberNotification($student, $invoice));

        return redirect()->back();
    }
}
