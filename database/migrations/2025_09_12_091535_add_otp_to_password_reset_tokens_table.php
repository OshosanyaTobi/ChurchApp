<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtpAndExpiresToPasswordResetTokensTable extends Migration
{
    public function up()
    {
        // protect against missing table (prevents errors)
        if (! Schema::hasTable('password_reset_tokens')) {
            return;
        }

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (! Schema::hasColumn('password_reset_tokens', 'otp')) {
                $table->string('otp')->nullable()->after('token');
            }
            if (! Schema::hasColumn('password_reset_tokens', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('otp');
            }
        });
    }

    public function down()
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (Schema::hasColumn('password_reset_tokens', 'otp')) {
                $table->dropColumn('otp');
            }
            if (Schema::hasColumn('password_reset_tokens', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
        });
    }
}
