<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Protect the first administrator record in existing databases.  This is
     * intended as a one‑time upgrade step for installations that predate the
     * automatic protection logic added earlier.  We only touch the table when
     * no admin is currently marked protected to avoid overriding manual flags.
     *
     * In combination with controller safeguards this prevents lock‑out even if
     * an already‑created admin lacked the flag.
     *
     * @return void
     */
    public function up()
    {
        $query = DB::table('users')->where('role', 'admin');

        // if a protected admin already exists nothing to do
        if ($query->where('is_protected', 1)->exists()) {
            return;
        }

        // mark the earliest admin row
        $firstAdmin = $query->orderBy('id')->first();
        if ($firstAdmin) {
            DB::table('users')->where('id', $firstAdmin->id)
                ->update(['is_protected' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * This doesn't un‑protect the user because doing so would effectively undo
     * the safety we just added, which is probably not desirable in a downgrade.
     * We'll leave it as a no‑op.
     *
     * @return void
     */
    public function down()
    {
        // intentionally left blank
    }
};
