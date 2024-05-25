<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserCreated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected User $user
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(): MailMessage
    {
        $appName = config('app.name');

        $name = str($this->user->name)->explode(' ')->first();

        return (new MailMessage)
            ->subject("Bem-vindo(a) ao {$appName}!")
            ->greeting("Olá, {$name}!")
            ->line("Seja bem-vindo(a) ao {$appName}! Obrigado por criar sua conta conosco!")
            ->action('Fazer login', filament()->getPanel('dash')->getLoginUrl())
            ->salutation(' ');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
