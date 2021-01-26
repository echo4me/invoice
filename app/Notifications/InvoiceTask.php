<?php

namespace App\Notifications;

use App\Invoices;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceTask extends Notification
{
    use Queueable;
    private $invoice ;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Invoices $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }



    public function toDatabase($notifiable)
    {
        return [
           'id'    => $this->invoice->id ,
           'title' => ' تم اضافة فاتورة جديده بواسطة : ' ,
           'user'  => auth()->user()->name ,

        ];
    }


}
