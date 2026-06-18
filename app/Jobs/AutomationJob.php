<?php

namespace App\Jobs;

use App\Mail\AutomationMail;
use App\Traits\WebhookNotify;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class AutomationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WebhookNotify;

    protected $sendEmailList;
    protected $mailServer;
    protected $params;

    /**
     * Create a new job instance.
     */
    public function __construct($sendEmailList, $APP_URL, $params = [])
    {
        $this->sendEmailList = $sendEmailList;
        $this->mailServer = optional(optional($sendEmailList->user)->plan)->sending_server ?? 'smtp';
        $this->params = $params;
        file_put_contents(base_path('.env'), str_replace(
            'APP_URL=' . config('app.url'),
            'APP_URL=' . $APP_URL,
            file_get_contents(base_path('.env'))
        ));
        Artisan::call('optimize:clear');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $htmlCode = $this->sendEmailList->template_html;
            if ($this->sendEmailList->field_data) {
                foreach ($this->sendEmailList->field_data as $code => $value) {
                    $htmlCode = str_replace('((' . $code . '))', $value, $htmlCode);
                }
            }
            $htmlCode = str_replace("((sender_name))", $this->sendEmailList->sender_name, $htmlCode);
            $htmlCode = str_replace("((sender_address))", $this->sendEmailList->company_address, $htmlCode);
            $htmlCode = str_replace(
                "((unsubscribe_preferences))",
                '<a href="' . route('automationUnsubscribe', [
                    'params' => Crypt::encrypt([$this->sendEmailList->id])
                ]) . '">Unsubscribe</a>',
                $htmlCode
            );

            Mail::mailer($this->mailServer)->to($this->sendEmailList->email_address)->send(new AutomationMail($this->sendEmailList, $htmlCode));
            $this->sendEmailList->delivered = 1;
            $this->sendEmailList->save();
            updateWallet($this->sendEmailList->user_id, 1, 'use_emails', 1);
            $this->pushStatusUserWebhook($this->sendEmailList->email_address, 'delivered', $this->sendEmailList->user ?? null);

            if (isset($this->sendEmailList->user->throttle) && $this->sendEmailList->user->throttle > 0) {
                sleep($this->sendEmailList->user->throttle);
            } else {
                if (isset($this->params['emailThrottleStatus']) && isset($this->params['emailThrottleTime'])) {
                    if ($this->params['emailThrottleStatus']) {
                        sleep($this->params['emailThrottleTime']);
                    }
                }
            }

        } catch (\Exception $e) {
            $this->sendEmailList->bounces = 1;
            $this->sendEmailList->save();
            $this->pushStatusUserWebhook($this->sendEmailList->email_address, 'bounces', $this->sendEmailList->user ?? null);
        }
    }

}
