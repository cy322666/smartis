<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Lead;
use App\Services\amoCRM\Client;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
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

        $amoApi = (new Client(Account::first()))->init();

        foreach ($leads as $model) {

            if ($model->first_click !== 'Остальное' &&
                $model->last_click  !== 'Остальное') {

                try {
                    $lead = $amoApi->service->leads()->find($model->lead_id);

                    if ($lead->cf('Ссылка Smartis')->getValue()) {

                        $lead->byId('945416')->setValue($model->first_click);
                        $lead->byId('129419')->setValue($model->last_click);
                        $lead->save();

                        $model->send = true;
                    }
                } catch (\Throwable $e) {

                    Log::error(__METHOD__, [$e->getMessage().' '.$e->getFile().' '.$e->getLine()]);
                }
            }

            $model->save();
        }

        return CommandAlias::SUCCESS;
    }
}
