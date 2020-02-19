<?php

namespace App\Jobs;

use App\Project;
use App\UserTransferLogs;
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
     * Create a new job instance.
     *
     * @param $fromUserID
     * @param $toUserID
     */
    public function __construct($fromUserID, $toUserID)
    {
        //
        $this->fromUserID = $fromUserID;
        $this->toUserID = $toUserID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $projectTransfer = [];
        //
        Project::with(['users'])
            ->whereHas('users', function($query) {
                $query->where('users.id', $this->fromUserID);
            })
            ->each(function ($item, $key) use (&$projectTransfer){
                array_push($projectTransfer, new TransferUserProject($item, $this->fromUserID, $this->toUserID));
            });

        ProcessUserDataTransfer::withChain($projectTransfer)
                                ->dispatch();

    }
}
