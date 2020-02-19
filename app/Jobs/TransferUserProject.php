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
    private $fromUser;
    private $toUser;
    private $fromUserName;
    private $toUserName;
    /**
     * @var UserTransferRequests
     */
    private $userTransferRequests;
    /**
     * @var int
     */
    private $projectCount;

    /**
     * Create a new job instance.
     *
     * @param Project $project
     * @param $fromUser
     * @param $toUser
     * @param UserTransferRequests $userTransferRequests
     * @param int $projectCount
     */
    public function __construct(Project $project, $fromUser, $toUser, UserTransferRequests $userTransferRequests, int $projectCount)
    {
        //
        $this->project = $project;
        $this->fromUser = $fromUser;
        $this->toUser = $toUser;
        $this->fromUserName = $this->project->users()->where('users.id', $fromUser)->value('name');
        $this->toUserName = User::find($toUser)->name;
        $this->userTransferRequests = $userTransferRequests;
        $this->projectCount = $projectCount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $this->project->users()->detach($this->fromUser);
        $this->project->users()->syncWithoutDetaching($this->toUser);

        // progress calculation
        $userTransfer = UserTransferRequests::find($this->userTransferRequests->id);
        $userTransfer->update([
           'project_transferred' => $userTransfer->project_transferred+1,
            'percentage' => (($userTransfer->project_transferred === 0 ? 1 : $userTransfer->project_transferred)/($this->projectCount-1))*100,
            'end_time' => Carbon::now()
        ]);

        UserTransferLogs::create([
            'module' => 'Project',
            'module_id' => $this->project->id,
            'from_user_id' => $this->fromUser,
            'from_user_name' => $this->fromUserName,
            'to_user_id' => $this->toUser,
            'to_user_name' => $this->toUserName
        ]);
    }
}
