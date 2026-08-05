<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class ResetUserSession extends Command
{
    protected $signature = 'session:logout-all';
    protected $description = 'Logs out all users by clearing all sessions';

    public function handle()
    {
        if (config('session.driver') === 'database') {
            DB::table('sessions')->truncate();
            $this->info('All users have been logged out (database sessions cleared).');
        } elseif (config('session.driver') === 'file') {
            array_map('unlink', glob(storage_path('framework/sessions/*')));
            $this->info('All users have been logged out (file sessions cleared).');
        } else {
            cache()->flush();
            $this->info('All users have been logged out (cache sessions cleared).');
        }
    }
}
