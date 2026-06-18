<?php

namespace App\Jobs;

use App\Mail\SingleSendMail;
use App\Traits\WebhookNotify;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class SingleSendEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WebhookNotify;

    protected $singleSend;
    protected $mailServer;
    protected $params;

    /**
     * Create a new job instance.
     */
    public function __construct($singleSend, $APP_URL, $params = [])
    {
        $this->singleSend = $singleSend;
        $this->mailServer = optional(optional($singleSend->user)->plan)->sending_server ?? 'smtp';
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
        $countDelivered = 0;
        $countTriggered = count($this->singleSend->recipientEmails());
        if ($countTriggered > 0) {
            foreach ($this->singleSend->recipientEmails() as $recipient) {
                try {
                    $htmlCode = $this->singleSend->template_html;
                    foreach ($recipient as $code => $value) {
                        $htmlCode = str_replace('((' . $code . '))', $value, $htmlCode);
                    }

                    $htmlCode = str_replace("((sender_name))", $this->singleSend->sender_name, $htmlCode);
                    $htmlCode = str_replace("((sender_address))", $this->singleSend->sender_address, $htmlCode);
                    $htmlCode = str_replace(
                        "((unsubscribe_preferences))",
                        '<a href="' . route('unsubscribePreference', [
                            'params' => Crypt::encrypt([$this->singleSend->user_id, $recipient['email'], $this->singleSend->id])
                        ]) . '">Unsubscribe Preferences</a>',
                        $htmlCode
                    );
                    try {

                        Mail::mailer($this->mailServer)->to($recipient['email'])->send(new SingleSendMail($this->singleSend, $htmlCode, $recipient['email']));
                        $countDelivered++;
                        singleSendActivitySave($this->singleSend->user_id, $this->singleSend->id, $recipient['email'], 1);
                        $this->pushStatusUserWebhook($recipient['email'], 'delivered', $this->singleSend->user ?? null);

                        if (isset($this->singleSend->user->throttle) && $this->singleSend->user->throttle > 0) {
                            sleep($this->singleSend->user->throttle);
                        } else {
                            if (isset($this->params['emailThrottleStatus']) && isset($this->params['emailThrottleTime'])) {
                                if ($this->params['emailThrottleStatus']) {
                                    sleep($this->params['emailThrottleTime']);
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        singleSendActivitySave($this->singleSend->user_id, $this->singleSend->id, $recipient['email'], 2);
                        $this->pushStatusUserWebhook($recipient['email'], 'bounces', $this->singleSend->user ?? null);
                        continue;
                    }

                } catch (\Exception $e) {
                    singleSendActivitySave($this->singleSend->user_id, $this->singleSend->id, $recipient['email'], 2);
                    $this->pushStatusUserWebhook($recipient['email'], 'bounces', $this->singleSend->user ?? null);
                    continue;
                }
            }
        }
        $this->singleSend->triggered = $countTriggered;
        $this->singleSend->delivered = $countDelivered;
        $this->singleSend->bounces = $countTriggered - $countDelivered;
        $this->singleSend->status = 3;
        $this->singleSend->save();

        updateWallet($this->singleSend->user_id, $countDelivered, 'use_emails', 1);
    }

}
