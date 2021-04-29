<?php

namespace Pixelvide\Ops\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ops:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the Ops resources';

    /**
     * Execute the console command
     *
     * @return void
     */
    public function handle()
    {
        $this->comment('Publishing Ops Configuration...');
        $this->callSilent('vendor:publish', [
            '--tag' => 'ops-config'
        ]);

        $this->info('Ops scaffolding installed successfully.');
    }
}