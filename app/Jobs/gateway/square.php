<?php

namespace App\Jobs\gateway;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class square implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $subscriptionPlan;
	protected $gateway;
	protected $type;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct($subscriptionPlan, $gateway, $type)
	{
		$this->subscriptionPlan = $subscriptionPlan;
		$this->gateway = $gateway;
		$this->type = $type;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		if ($this->type == 'create') {
			$getwayObj = 'App\\Services\\Subscription\\' . $this->gateway->code . '\\Payment';
			$getwayObj::createPlan($this->gateway, $this->subscriptionPlan);
		} elseif ($this->type == 'update') {
			$getwayObj = 'App\\Services\\Subscription\\' . $this->gateway->code . '\\Payment';
			$getwayObj::updatePlan($this->gateway, $this->subscriptionPlan);
		} elseif ($this->type == 'active') {
			$getwayObj = 'App\\Services\\Subscription\\' . $this->gateway->code . '\\Payment';
			$getwayObj::activatedPlan($this->gateway, $this->subscriptionPlan);
		}elseif ($this->type == 'deactive') {
			$getwayObj = 'App\\Services\\Subscription\\' . $this->gateway->code . '\\Payment';
			$getwayObj::deActivatedPlan($this->gateway, $this->subscriptionPlan);
		}
	}
}
