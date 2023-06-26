<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Lead;
use App\Services\amoCRM\Client;
use Exception;
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
     * @throws Exception
     */
    public function handle(): int
    {
        $leads = Lead::query()
            ->where('first_click', '!=', null)
            ->where('last_click', '!=', null)
            ->where('send', false)
            ->limit(env('LIMIT_SEND'))
            ->get();

        foreach ($leads as $model) {

            if ($model->first_click !== 'Остальное' &&
                $model->last_click  !== 'Остальное') {

                $amoApi = (new Client(Account::first()))->init();

                $lead = $amoApi->service->leads()->find($model->lead_id);

                $lead->cf('')->setValue();
                $lead->cf('')->setValue();
                $lead->save();
            }

            $model->send = true;
            $model->save();
        }

        return CommandAlias::SUCCESS;
    }
}
