<?php

namespace Modules\OutreachManagement\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\EmailNotificationSetting;
use App\User;
use Modules\OutreachManagement\Entities\Invoice;

class InvoiceNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $invoice;
    private $headmessage;
    private $bodymessage;
    private $deleted;

    public function __construct(Invoice $invoice, $headmessage, $bodymessage, $deleted = false)
    {
        $this->invoice = $invoice;
        $this->headmessage = $headmessage;
        $this->bodymessage = $bodymessage;
        $this->deleted = $deleted;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if ($notifiable->hasRole('admin')) {
            $url = route('admin.outreach-invoices.index');
        } else {
            $url = route('member.outreach-invoices.index');
        }
        
        if ($this->invoice->receipt && $this->invoice->status) {
            $file = public_path('user-uploads/'.$this->invoice->receipt);

            return (new MailMessage)
            ->subject($this->headmessage . ' #' . $this->invoice->id . ' - ' . config('app.name'))
            ->from('outreach@viserx.com', auth()->user()->name .' via ' . config('app.name'))
            ->markdown('outreachmanagement::mail.invoice', ['invoice' => $this->invoice, 'url' => $url, 'headmessage' => $this->headmessage, 'bodymessage' => $this->bodymessage, 'deleted' => $this->deleted])
            ->attach($file, ['as' => $this->invoice->name.'_receipt.'.pathinfo($file, PATHINFO_EXTENSION), 'mime' => mime_content_type($file)]);
        } else {
            return (new MailMessage)
            ->subject($this->headmessage . ' #' . $this->invoice->id . ' - ' . config('app.name'))
            ->from('outreach@viserx.com', auth()->user()->name .' via ' . config('app.name'))
            ->markdown('outreachmanagement::mail.invoice', ['invoice' => $this->invoice, 'url' => $url, 'headmessage' => $this->headmessage, 'bodymessage' => $this->bodymessage, 'deleted' => $this->deleted]);
        }

    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
