<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollaboratorTransfer;
use App\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use function foo\func;

class MigrationController extends Controller
{
    /**
     * @param CollaboratorTransfer $request
     * @return Builder[]|Collection
     */
    public function transferUserData(CollaboratorTransfer $request) {

        $projectUser =  Project::with(['users'])
                                ->whereHas('users', function($query) use ($request) {
                                    $query->where('users.id', $request->input('from_user_id'));
                                })
                                ->get();

        foreach ($projectUser as $project){
            $project->users()->detach($request->input('from_user_id'));
            $project->users()->attach($request->input('to_user_id'));
        }


        return $projectUser->load(['users']);

    }
}
