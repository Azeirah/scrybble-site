<?php
declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('gumroad_license', static function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class);
            $table->string('license');

            $table->timestamps();
        });
    }
};
