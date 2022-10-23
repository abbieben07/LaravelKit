<?php

namespace Novacio\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GithubJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

    public function __construct()
    {
        //
    }

    public function handle(string $key)
    {
        $dir = base_path();
        $prefix = "cd {$dir} && sudo ";
        shell_exec("{$prefix}git stash");
        shell_exec("{$prefix}git pull");
        shell_exec("{$prefix}composer install");
        shell_exec("{$prefix}npm install");
        shell_exec("{$prefix}php artisan env:decrypt --key={$key} --env=development");
        shell_exec("{$prefix}php artisan ziggy:generate");
        shell_exec('export NODE_OPTIONS=--max_old_space_size=4096');
        shell_exec("{$prefix}npm run dev");
        shell_exec("{$prefix}php artisan optimize");
        shell_exec("{$prefix}php artisan migrate");
    }
}
