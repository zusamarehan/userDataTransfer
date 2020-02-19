<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollaboratorTransfer;
use App\Jobs\TransferUserData;
use App\Project;
use App\UserTransferLogs;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class MigrationController extends Controller
{
    /**
     * @param CollaboratorTransfer $request
     * @return string
     */
    public function transferUserData(CollaboratorTransfer $request) {

        TransferUserData::dispatch($request->input('from_user_id'), $request->input('to_user_id'));

        return 'Migration is in Process';

    }

    /**
     * @return Builder[]|Collection
     */
    public function getTransferHistory() {

        return UserTransferLogs::get();
    }
}

