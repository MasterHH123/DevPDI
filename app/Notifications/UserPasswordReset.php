<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class UserPasswordReset extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $new_pass;

    public function __construct($new_pass)
    {
        $this->new_pass = $new_pass;
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
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
        ->subject('Cambio de clave | ' . config('app.name') )
        ->greeting("Hola {$notifiable->first_name}!")
        ->line("Parece que nos solicitaste el cambio tu clave de acceso. Puedes usar la siguiente clave temporal para ingresar:")
        ->line( new HtmlString("<b style='color:#8E24AA;background:#EDE7F6;width:10em;text-align:center;display:block;margin:auto;'>{$this->new_pass}</b>"))
        ->action('Ir a la plataforma', url('/'))
        ->salutation(' ');
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
