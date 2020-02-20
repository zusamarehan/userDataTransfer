<?php

namespace App\Jobs;

use App\UserTransferRequests;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EndTransferUserData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var UserTransferRequests
     */
    private $userTransferRequest;

    /**
     * Create a new job instance.
     *
     * @param UserTransferRequests $userTransferRequest
     */
    public function __construct(UserTransferRequests $userTransferRequest)
    {
        //
        $this->userTransferRequest = $userTransferRequest;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $this->userTransferRequest->update([
            'end_time' => Carbon::now()
        ]);
    }
}
