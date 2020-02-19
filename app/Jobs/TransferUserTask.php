<?php

namespace App\Jobs;

use App\Tasks;
use App\User;
use App\UserTransferLogs;
use App\UserTransferRequests;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TransferUserTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Tasks
     */
    private $tasks;
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
    private $tasksCount;

    /**
     * Create a new job instance.
     *
     * @param Tasks $tasks
     * @param $fromUser
     * @param $toUser
     * @param UserTransferRequests $userTransferRequests
     * @param int $tasksCount
     */
    public function __construct(Tasks $tasks, $fromUser, $toUser, UserTransferRequests $userTransferRequests, int $tasksCount)
    {
        //
        $this->tasks = $tasks;
        $this->fromUser = $fromUser;
        $this->toUser = $toUser;
        $this->fromUserName = $this->tasks->users()->where('users.id', $fromUser)->value('name');
        $this->toUserName = User::find($toUser)->name;
        $this->userTransferRequests = $userTransferRequests;
        $this->tasksCount = $tasksCount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $this->tasks->users_id = $this->toUser;
        $this->tasks->save();

        // progress calculation
        $userTransfer = UserTransferRequests::find($this->userTransferRequests->id);
        $userTransfer->update([
            'task_transferred' => $userTransfer->task_transferred+1,
            'percentage' => 50 + (($userTransfer->task_transferred === 0 ? 1 : $userTransfer->task_transferred)/($this->tasksCount-1)) * 50,
            'end_time' => Carbon::now()
        ]);

        UserTransferLogs::create([
            'module' => 'Tasks',
            'module_id' => $this->tasks->id,
            'from_user_id' => $this->fromUser,
            'from_user_name' => $this->fromUserName,
            'to_user_id' => $this->toUser,
            'to_user_name' => $this->toUserName
        ]);
    }
}
