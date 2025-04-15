<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $status,
        public string $action
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Seu pedido foi {$this->status}")
            ->line("O status do seu pedido foi alterado para: {$this->status}")
            ->action('Ver Pedido', url('/orders'))
            ->line("Ação: {$this->action}");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "O status do seu pedido foi alterado para: {$this->status}",
            'action' => $this->action,
            'status' => $this->status,
            'url' => '/orders'
        ];
    }
}
