<?php

namespace App\Console\Commands;

use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Laravel\Octane\Exceptions\DdException;
use Symfony\Component\Console\Command\Command as CommandAlias;

class GetInfo2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smartis:info2';

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
    public function handle(): int
    {
        $first = Lead::query()
            ->where('send', false)
            ->where('last_click', null)
            ->orderByDesc('datetime')
            ->first();

        $latest = Lead::query()
            ->where('send', false)
            ->orderBy('datetime')
            ->first();

        $dateTo   = Carbon::now()->format('Y-m-d H:i:s');
        $dateFrom = Carbon::now()->subYears(3)->format('Y-m-d H:i:s');

        $smartisResponse = Http::withToken(env('SMARTIS_TOKEN'))
            ->timeout(300)
            ->post('https://my.smartis.bi/api/reports/getReport', [
                "project" => "object_2369",
                "metrics" => "crm_amo_all;",
                "groupBy" => "day",
                "datetimeFrom" => $dateFrom,
                "datetimeTo"   => $dateTo,
                "type"     => "raw",
                "filters" => [
                    [
                        "filter_category"  => 36213,
                        "filter_condition" => "!=",
                        "filter_value"     => false
                    ]
                ],
                "attribution" => [
                    "model_id"    => 2,
                    "period"      => "365",
                    "with_direct" => true
                ],
                "topCount" => 500,
            ]);

        $smartis = json_decode($smartisResponse->body());

        foreach ($smartis->reports->crm_amo_all as $detail) {

            $lead = Lead::query()
                ->where('lead_id', $detail->external_id)
                ->first();

            if ($lead->exists()) {

                $lead->fill(['first_click' => $detail->sources_placements]);
                $lead->save();
            }
        }

        return CommandAlias::SUCCESS;
    }
}
