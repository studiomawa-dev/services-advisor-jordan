<?php
namespace App\Listeners;
use Illuminate\Auth\Events\Failed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User as User;
use Auth;
use Hash;

class ResetPasswordAuthenticationAttempt
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    /**
     * Handle the event.
     *
     * @param  Failed  $event
     * @return void
     */
    public function handle(Failed $event)
    {
      $credentials = isset($event->credentials['username']) ? $event->credentials['username'] : $event->credentials['email'];
      $user = User::where('email', $credentials)->orWhere('username', $credentials)->first();

      if($user != null && $user->password == '$2y$10$lbtV7IjAmU.yBSaPmqqVhuJkTg0C1ahsPFz2tfK/VqIHSQB8CKFre') {
      	abort(redirect('/password/reset')->with('warning','Please reset your password to proceed with login.'));
      }

    }
}
