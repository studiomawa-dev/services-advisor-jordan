<?php
namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\ExportReady;

class NotifyUserOfExport implements ShouldQueue
{
	use Queueable, SerializesModels;

	public $user;

	public function __construct(User $user, $name)
	{
		$this->user = $user;
	}

	public function handle()
	{
		$this->user->notify(new ExportReady());
	}
}
