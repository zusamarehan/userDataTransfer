<?php

namespace App\Jobs;

use App\Project;
use App\UserTransferRequests;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessUserProjectTransfer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $projectPercentage = 50;
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
        //
        $projectTransfer = [];
        //
        $projects = Project::with(['users'])
            ->whereHas('users', function($query) {
                $query->where('users.id', $this->fromUserID);
            });

        $projects->each(function ($item, $key) use (&$projectTransfer, $projects){
            array_push($projectTransfer, new TransferUserProject($item, $this->fromUserID, $this->toUserID, $this->userTransferRequests, $projects->count()));
        });

        // If $projectTransfer no data, then the collaborator ID is not assigned to any projects
        // no need to do any transfer just end the request
        if(count($projectTransfer) <= 0) {

            $userTransfer = UserTransferRequests::find($this->userTransferRequests->id);
            $userTransfer->update([
                'project_transferred' => 0,
                'percentage' => $userTransfer->percentage + $this->projectPercentage,
                'end_time' => Carbon::now()
            ]);

        }
        else {

            ProcessUserDataTransfer::withChain($projectTransfer)
                ->dispatch();

        }
    }
}
