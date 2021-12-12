<?php

namespace Modules\OutreachManagement\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\EmailNotificationSetting;
use App\User;
use Modules\OutreachManagement\Entities\Backlink;

class BacklinkNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $backlink;
    private $headmessage;
    private $bodymessage;
    private $deleted;

    public function __construct(Backlink $backlink, $headmessage, $bodymessage, $deleted = false)
    {
        $this->backlink = $backlink;
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
            $url = route('admin.outreach-backlinks.index');
        } else {
            $url = route('member.outreach-backlinks.index');
        }
        
        return (new MailMessage)
        ->subject($this->headmessage . ' #' . $this->backlink->id . ' - ' . config('app.name'))
        ->from('outreach@viserx.com', auth()->user()->name .' via ' . config('app.name'))
        ->markdown('outreachmanagement::mail.backlink', ['backlink' => $this->backlink, 'url' => $url, 'headmessage' => $this->headmessage, 'bodymessage' => $this->bodymessage, 'deleted' => $this->deleted]);

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
