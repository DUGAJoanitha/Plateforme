<?php

namespace Tests\Unit;

use App\Models\User;
use App\Notifications\SystemAlertNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Kreait\Firebase\Messaging\CloudMessage;
use Tests\TestCase;

class SystemAlertNotificationTest extends TestCase
{
    public function test_it_routes_to_specified_channels()
    {
        $notification = new SystemAlertNotification('Test Title', 'Test Message', ['mail', 'database', 'vonage', 'firebase']);
        
        $this->assertEquals(['mail', 'database', 'vonage', 'firebase'], $notification->via(new User()));
    }

    public function test_it_builds_mail_message()
    {
        $notification = new SystemAlertNotification('Test Title', 'Test Message', ['mail'], 'http://example.com');
        $mail = $notification->toMail(new User());

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertEquals('PISE-PP Alerte: Test Title', $mail->subject);
        $this->assertEquals('http://example.com', $mail->actionUrl);
    }

    public function test_it_builds_vonage_message()
    {
        $notification = new SystemAlertNotification('Test Title', 'Test Message', ['vonage']);
        $vonage = $notification->toVonage(new User());

        $this->assertInstanceOf(VonageMessage::class, $vonage);
        // We can assert the payload contains the text
    }

    public function test_it_builds_firebase_message()
    {
        $user = new User(['fcm_token' => 'dummy_token']);
        $notification = new SystemAlertNotification('Test Title', 'Test Message', ['firebase']);
        $firebase = $notification->toFirebase($user);

        $this->assertInstanceOf(CloudMessage::class, $firebase);
    }
}
