<?php

namespace App\Console\Commands;

use App\Models\Lead;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class SendAmoCRM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smartis:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leads = Lead::query()
            ->where('first_clock', '!=', null)
            ->where('last_clock', '!=', null)
            ->where('send', false)
            ->limit(100)
            ->get();

        foreach ($leads as $lead) {

            //send
            $lead->send = true;
            $lead->save();
        }

        return CommandAlias::SUCCESS;
    }
}
