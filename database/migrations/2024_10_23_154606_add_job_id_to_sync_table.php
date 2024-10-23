<?php

use App\Models\Sync;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sync', function (Blueprint $table) {
            $table->renameColumn('user_storage_download_path', 'job_id');
        });
        Sync::where('job_id', "like", "userZips%")->each(function (Sync $sync) {
            // remove "userZips" from the start of the string:
            $sync->job_id = str_replace("userZips/", "", $sync->job_id);
            $sync->job_id = str_replace(".zip", "", $sync->job_id);
            $sync->save();
        });
    }
};
