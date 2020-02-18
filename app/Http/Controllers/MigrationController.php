<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollaboratorTransfer;
use App\Project;
use App\UserTransferLogs;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class MigrationController extends Controller
{
    /**
     * @param CollaboratorTransfer $request
     * @return Builder[]|Collection
     */
    public function transferUserData(CollaboratorTransfer $request) {

        $logging = [];
        $projectUser =  Project::with(['users'])
                                ->whereHas('users', function($query) use ($request) {
                                    $query->where('users.id', $request->input('from_user_id'));
                                })
                                ->get();

        foreach ($projectUser as $project){

            $project->users()->detach($request->input('from_user_id'));
            $project->users()->attach($request->input('to_user_id'));

            array_push($logging, [
                'module' => 'Project',
                'module_id' => $project->id,
                'from_user_id' => $request->input('from_user_id'),
                'to_user_id' => $request->input('to_user_id'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

        UserTransferLogs::insert($logging);

        return $projectUser->load(['users']);

    }

    /**
     * @return Builder[]|Collection
     */
    public function getTransferHistory() {

        return UserTransferLogs::get();
    }
}
