<?php

namespace App\Notifications;

use App\Invoice;
use App\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Class MonthlyMemberNotification
 * @package App\Notifications
 */
class MonthlyMemberNotification extends Notification
{
    use Queueable;
    /**
     * @var Student
     */
    private $student;
    /**
     * @var string
     */
    private $invoiceFullPath;
    /**
     * @var Invoice
     */
    private $invoice;

    /**
     * Create a new notification instance.
     *
     * @param Student $student
     * @param Invoice $invoice
     */
    public function __construct(Student $student, Invoice $invoice)
    {
        $this->student         = $student;
        $this->invoice         = $invoice;
        $this->invoiceFullPath = storage_path(
            'app' . DIRECTORY_SEPARATOR . Invoice::FOLDER . DIRECTORY_SEPARATOR . 'invoice_' . $invoice->invoice_number
        ) . '.pdf';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->view('emails.monthlyEmail', [
                'student' => $this->student,
                'invoice' => $this->invoice
            ])
            ->subject('Monthly invoice of attendance for ' . $this->invoice->period_from . ' to ' . $this->invoice->period_to)
            ->attach($this->invoiceFullPath)
            ->from('no-reply@primary-school.com');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
