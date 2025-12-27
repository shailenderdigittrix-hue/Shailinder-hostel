<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    protected $signature = 'email:test';
    protected $description = 'Send a test email to verify SMTP settings';

    public function handle()
    {
        $to = 'shailender.digittrix@gmail.com';

        try {
            Mail::raw('This is a test email from Laravel.', function($message) use ($to) {
                $message->to($to)
                        ->subject('Test Email from Laravel')
                        ->from('shailender.digittrix@gmail.com', 'ITIMandi Team');
            });

            $this->info("Test email sent to {$to}");
        } catch (\Exception $e) {
            $this->error("Failed to send email: " . $e->getMessage());
        }
    }
}
