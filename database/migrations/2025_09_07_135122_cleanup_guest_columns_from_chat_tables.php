<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cleanup chat_rooms table
        Schema::table('chat_rooms', function (Blueprint $table) {
            // Drop guest-related columns if they exist
            if (Schema::hasColumn('chat_rooms', 'is_guest')) {
                $table->dropColumn('is_guest');
            }
            if (Schema::hasColumn('chat_rooms', 'guest_session_id')) {
                $table->dropColumn('guest_session_id');
            }
            if (Schema::hasColumn('chat_rooms', 'guest_name')) {
                $table->dropColumn('guest_name');
            }
            if (Schema::hasColumn('chat_rooms', 'guest_email')) {
                $table->dropColumn('guest_email');
            }
        });

        // Cleanup chat_messages table
        Schema::table('chat_messages', function (Blueprint $table) {
            // Drop guest-related columns if they exist
            if (Schema::hasColumn('chat_messages', 'is_from_guest')) {
                $table->dropColumn('is_from_guest');
            }
        });

        // Cleanup chat_room_participants table
        Schema::table('chat_room_participants', function (Blueprint $table) {
            // Drop guest-related columns if they exist
            if (Schema::hasColumn('chat_room_participants', 'is_guest')) {
                $table->dropColumn('is_guest');
            }
            
            // Reset role enum to original values (if needed)
            $table->enum('role', ['customer', 'agent'])->default('customer')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a cleanup migration, no need to reverse
        // The proper guest support will be added in future migrations
    }
};
