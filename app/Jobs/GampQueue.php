<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GampQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $clientId;
    protected $category;
    protected $action;
    protected $label;

    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($clientId, $category, $action, $label)
    {
        $this->clientId = $clientId;
        $this->category = $category;
        $this->action   = $action;
        $this->label    = $label;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = Http::post('https://ga4post.simai.life', [
            "events"  => [
                [
                  "name" => "xstatics",
                  "params" => [
                    "category"=>  $this->category,
                    "action"=>  $this->action,
                    "label"=>  $this->label,
                  ]
                ],
            ],
            "measurementId" => "G-QWMQ95N4KG",
            "apiSecret" => "na9T69s9S16K7CAuiUtuEw",
            "clientId" => "552440170.1677742141",
        ]);
    }
}
