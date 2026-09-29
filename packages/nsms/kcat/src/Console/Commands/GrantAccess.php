<?php
namespace Nsms\Kcat\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GrantAccess extends Command
{
    protected $signature = 'kcat:grant {email} {role : counselor, principal, student, or none}';
    protected $description = 'Grant or revoke KCAT-only access for an existing website account';
    public function handle(): int
    {
        $role = $this->argument('role');
        if (! in_array($role, ['counselor', 'principal', 'student', 'none'], true)) {
            $this->error('Role must be counselor, principal, student, or none.'); return self::FAILURE;
        }
        $user = config('auth.providers.users.model')::where('email', $this->argument('email'))->first();
        if (! $user) { $this->error('Create this account through the website first.'); return self::FAILURE; }
        if ($role === 'none') { DB::table('kcat_access')->where('user_id', $user->id)->delete(); }
        else { DB::table('kcat_access')->updateOrInsert(['user_id' => $user->id], ['role' => $role]); }
        $this->info('KCAT access updated. Website roles and password were not changed.');
        return self::SUCCESS;
    }
}
