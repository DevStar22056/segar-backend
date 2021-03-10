<?php

namespace App\Mail;

use App\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Invoice
     */
    public $invoice;
    public $subject;
    public $body;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice, $subject, $body)
    {
        $this->invoice = $invoice;
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $invoice = $this->invoice;
//        $body = $this->body;
//        $path = Storage::url($invoice->file);

        return $this->from('invoices@seargin.com')
            ->cc('invoice@seargin.com')
            ->replyTo('invoice@seargin.com')
            ->view('emails.invoices.clientInvoice')
            ->subject($this->subject)
            ->attachFromStorage($invoice->file, $invoice->internal_invoice_number . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
