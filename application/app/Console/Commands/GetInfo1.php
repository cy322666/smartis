<?php

namespace App\Console\Commands;

use App\Models\Lead;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Laravel\Octane\Exceptions\DdException;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Throwable;

class GetInfo1 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smartis:info1';

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
     * @throws DdException
     */
    public function handle(): int
    {
        $latest = Lead::query()
            ->latest('date')
            ->where('send', false)
            ->first();

        $latestDate = $latest ? Carbon::parse($latest->datetime) : Carbon::now();

        $dateTo   = $latestDate->format('Y-m-d H:i:s');
        $dateFrom = $latest ?
                    $latestDate->subDays(3)->format('Y-m-d H:i:s') :
                    Carbon::now()->subDays(365);

        $smartisResponse = Http::withToken(env('SMARTIS_TOKEN'))
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
                    "model_id"    => 1,
                    "period"      => "365",
                    "with_direct" => true
                ],
                "topCount" => 500,
            ]);

        $smartis = json_decode($smartisResponse->body());

        foreach ($smartis->reports->crm_amo_all as $detail) {

            Lead::query()->firstOrCreate([
                'person_id' => $detail->person_id,
            ], [
                'datetime'    => Carbon::parse($detail->created_at)->format('Y-m-d H:i:s'),
                'date'        => $detail->date,
                'person_id'   => $detail->person_id,
                'smartis_id'  => $detail->id,
                'lead_id'     => $detail->external_id,
                'first_click' => $detail->sources_placements,
            ]);
        }

        return CommandAlias::SUCCESS;
    }
}
