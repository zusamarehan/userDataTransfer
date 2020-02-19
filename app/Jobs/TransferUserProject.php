<?php

namespace App\Jobs;

use App\Project;
use App\User;
use App\UserTransferLogs;
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
     * Create a new job instance.
     *
     * @param Project $project
     * @param $fromUser
     * @param $toUser
     */
    public function __construct(Project $project, $fromUser, $toUser)
    {
        //
        $this->project = $project;
        $this->fromUser = $fromUser;
        $this->toUser = $toUser;
        $this->fromUserName = $this->project->users()->where('users.id', $fromUser)->value('name');
        $this->toUserName = $this->project->users()->where('users.id', $toUser)->value('name');
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
