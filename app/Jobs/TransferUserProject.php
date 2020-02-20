<?php

namespace App\Jobs;

use App\Project;
use App\User;
use App\UserTransferLogs;
use App\UserTransferRequests;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TransferUserProject implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var Project
     */
    private $project;
    private $fromUserID;
    private $toUserID;
    private $fromUserName;
    private $toUserName;
    /**
     * @var UserTransferRequests
     */
    private $userTransferRequests;

    /**
     * Create a new job instance.
     *
     * @param Project $project
     * @param $fromUserID
     * @param $toUserID
     * @param UserTransferRequests $userTransferRequests
     * @param int $projectCount
     */
    public function __construct(Project $project, $fromUserID, $toUserID, UserTransferRequests $userTransferRequests)
    {
        //
        $this->project = $project;

        $this->fromUserID = $fromUserID;
        $this->toUserID = $toUserID;

        $this->fromUserName = $this->project->users()->where('users.id', $fromUserID)->value('name');
        $this->toUserName = User::find($toUserID)->name;

        $this->userTransferRequests = $userTransferRequests;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Project Transfer
        $this->project->users()->detach($this->fromUserID);
        $this->project->users()->syncWithoutDetaching($this->toUserID);
        // Project Task Transfer
        $this->project->tasks()->where('tasks.user_id', $this->fromUserID)->update(['tasks.user_id' => $this->toUserID]);

        // Transfer Progress Calculation
        $this->userTransferRequests->update([
            'project_transferred' => $this->userTransferRequests->project_transferred+1,
            'percentage' => (($this->userTransferRequests->project_transferred+1)/$this->userTransferRequests->project_transfer) * 100,
        ]);

        // Transfer Logs
        UserTransferLogs::create([
            'module' => 'Project',
            'module_id' => $this->project->id,
            'from_user_id' => $this->fromUserID,
            'from_user_name' => $this->fromUserName,
            'to_user_id' => $this->toUserID,
            'to_user_name' => $this->toUserName
        ]);
    }
}
