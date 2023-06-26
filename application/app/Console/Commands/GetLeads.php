<?php

namespace App\Console\Commands;

use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Command\Command as CommandAlias;

class GetLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smartis:leads';

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
//        $dateTo = Carbon::parse($first->datetime ?? Carbon::now())->format('Y-m-d H:i:s');
//        $dateFrom = Carbon::parse($latest->datetime ?? Carbon::now()->subYears(3))->format('Y-m-d H:i:s');

        $dateTo   = Carbon::now()->format('Y-m-d H:i:s'); //$latestDate->format('Y-m-d H:i:s');
        $dateFrom = Carbon::now()->subDays(365);

        $smartisResponse = Http::withToken(env('SMARTIS_TOKEN'))
            ->timeout(300)
            ->post('https://my.smartis.bi/api/reports/getReport', [
                "project"    => "object_2369",
                "metrics"    => "crm_amo_all;",
                "groupBy"    => "day",
                "datetimeTo" => $dateTo,
                "datetimeFrom" => $dateFrom,
                "type"       => "raw",
                "filters"    => [
                    [
                        "filter_category"  => 36213,
                        "filter_condition" => "not_empty",
                        "filter_value"     => ""
                    ]
                ],
                "attribution"  => [
                    "model_id" => 2,
                    "period"   => "365",
                    "with_direct" => true
                ],
                "topCount" => 500,
            ]);

        $smartis = json_decode($smartisResponse->body());

        foreach ($smartis->reports->crm_amo_all as $detail) {

            Lead::query()->create([
                'lead_id'    => $detail->external_id,
                'datetime'   => Carbon::parse($detail->created_at)->format('Y-m-d H:i:s'),
                'date'       => $detail->date,
                'person_id'  => $detail->person_id,
                'smartis_id' => $detail->id,
            ]);

        }
        return CommandAlias::SUCCESS;
    }
}
