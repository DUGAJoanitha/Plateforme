<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\VonageMessage;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class SystemAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $title;
    public $message;
    public $channels;
    public $actionUrl;

    /**
     * Create a new notification instance.
     *
     * @param string $title
     * @param string $message
     * @param array $channels ['mail', 'database', 'vonage', 'firebase']
     * @param string|null $actionUrl
     */
    public function __construct(string $title, string $message, array $channels = ['database'], ?string $actionUrl = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->channels = $channels;
        $this->actionUrl = $actionUrl;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $this->channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('PISE-PP Alerte: ' . $this->title)
            ->line($this->message);

        if ($this->actionUrl) {
            $mail->action('Voir les détails', $this->actionUrl);
        }

        return $mail;
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
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
        ];
    }

    /**
     * Get the Vonage / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\VonageMessage
     */
    public function toVonage($notifiable)
    {
        return (new VonageMessage)
            ->content($this->title . ': ' . $this->message);
    }

    /**
     * Get the Firebase Push representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toFirebase($notifiable)
    {
        return CloudMessage::new()->withToken($notifiable->fcm_token ?? '')
            ->withNotification(FirebaseNotification::create($this->title, $this->message))
            ->withData([
                'action_url' => $this->actionUrl ?? '',
            ]);
    }
}
