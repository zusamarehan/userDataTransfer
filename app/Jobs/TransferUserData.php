<?php

namespace App\Jobs;

use App\Project;
use App\UserTransferLogs;
use App\UserTransferRequests;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TransferUserData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $fromUserID;
    private $toUserID;
    /**
     * @var UserTransferRequests
     */
    private $userTransferRequests;

    /**
     * Create a new job instance.
     *
     * @param $fromUserID
     * @param $toUserID
     * @param UserTransferRequests $userTransferRequests
     */
    public function __construct($fromUserID, $toUserID, UserTransferRequests $userTransferRequests)
    {
        //
        $this->fromUserID = $fromUserID;
        $this->toUserID = $toUserID;
        $this->userTransferRequests = $userTransferRequests;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $projects = Project::with(['users'])
            ->whereHas('users', function($query) {
                $query->where('users.id', $this->fromUserID);
            });

        $projects->each(function ($item, $key) use ($projects){
            TransferUserProject::dispatch($item, $this->fromUserID, $this->toUserID, $this->userTransferRequests);
        });

        EndTransferUserData::dispatch($this->userTransferRequests);

    }
}
