<?php

namespace App\Console\Commands;

use App\Invoice;
use App\Student;
use Illuminate\Console\Command;
use LaravelDaily\Invoices\Classes\Party;
use Illuminate\Support\Facades\Notification;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Invoice as InvoicePDF;
use App\Notifications\MonthlyMemberNotification;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class SendInvoicesCommand
 * @package App\Console\Commands
 */
class SendInvoicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send invoices';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws BindingResolutionException
     */
    public function handle()
    {
        $lastMonth = now()->subMonth();

        $students = Student::query()
            ->whereHas('attendances', function ($q) use ($lastMonth) {
                return $q->whereYear('event_date', $lastMonth->year)
                    ->whereMonth('event_date', $lastMonth->month);
            })
            ->withCount(['attendances' => function ($q) use ($lastMonth) {
                return $q->whereYear('event_date', $lastMonth->year)
                    ->whereMonth('event_date', $lastMonth->month);
            }])
            ->get();

        $seller = new Party([
            'name' => 'Private Primary School',
        ]);

        $invoiceNumber          = $this->getLastInvoiceNumber() + 1;
        $generatedInvoicesCount = 0;

        foreach ($students as $student) {
            $amountToPay = config('invoices.daily_rate') * $student->attendances_count;

            $invoice = Invoice::create([
                'period_from'    => $lastMonth->startOfMonth()->toDateString(),
                'period_to'      => $lastMonth->endOfMonth()->toDateString(),
                'invoice_number' => $invoiceNumber,
                'total_amount'   => $amountToPay,
                'student_id'     => $student->id,
            ]);

            $customer = new Party([
                'name'          => $student->fullName,
                'address'       => $student->billing_address,
                'custom_fields' => [
                    'email' => $student->email,
                ],
            ]);

            $item = (new InvoiceItem())
                ->title(config('app.name') . ' - invoice for ' .  $lastMonth->format('F Y'))
                ->pricePerUnit($amountToPay);

            InvoicePDF::make()
                ->seller($seller)
                ->buyer($customer)
                ->sequence($invoiceNumber)
                ->filename(Invoice::FOLDER . DIRECTORY_SEPARATOR . 'invoice_' . $invoiceNumber)
                ->addItem($item)
                ->save();

            Notification::route('mail', $student->email)->notify(new MonthlyMemberNotification($student, $invoice));

            $invoiceNumber++;
            $generatedInvoicesCount++;
        }

        $this->info('Total invoices generated and sent: ' . $generatedInvoicesCount);
    }

    /**
     * @return mixed
     */
    private function getLastInvoiceNumber()
    {
        return Invoice::query()->max('invoice_number');
    }
}
