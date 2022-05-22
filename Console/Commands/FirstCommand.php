<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FirstCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'first:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is my first commend. and working this commend';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
//        return $this->info("Run this commend");
    }
}
