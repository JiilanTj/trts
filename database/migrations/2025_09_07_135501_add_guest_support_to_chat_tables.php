<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add guest support to chat_rooms table
        Schema::table('chat_rooms', function (Blueprint $table) {
            // Make user_id nullable to support guest chats
            if (!Schema::hasColumn('chat_rooms', 'user_id') || 
                DB::getSchemaBuilder()->getColumnType('chat_rooms', 'user_id') !== 'bigint') {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            }
            
            // Add guest-related columns
            if (!Schema::hasColumn('chat_rooms', 'is_guest')) {
                $table->boolean('is_guest')->default(false)->after('user_id');
            }
            if (!Schema::hasColumn('chat_rooms', 'guest_session_id')) {
                $table->string('guest_session_id', 100)->nullable()->after('is_guest');
            }
            if (!Schema::hasColumn('chat_rooms', 'guest_name')) {
                $table->string('guest_name', 100)->nullable()->after('guest_session_id');
            }
            if (!Schema::hasColumn('chat_rooms', 'guest_email')) {
                $table->string('guest_email', 150)->nullable()->after('guest_name');
            }
        });

        // Add guest support to chat_messages table
        Schema::table('chat_messages', function (Blueprint $table) {
            // Make user_id nullable to support guest messages
            if (!Schema::hasColumn('chat_messages', 'user_id') || 
                DB::getSchemaBuilder()->getColumnType('chat_messages', 'user_id') !== 'bigint') {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            }
            
            // Add guest flag
            if (!Schema::hasColumn('chat_messages', 'is_from_guest')) {
                $table->boolean('is_from_guest')->default(false)->after('user_id');
            }
        });

        // Add guest support to chat_room_participants table
        Schema::table('chat_room_participants', function (Blueprint $table) {
            // Make user_id nullable to support guest participants
            if (!Schema::hasColumn('chat_room_participants', 'user_id') || 
                DB::getSchemaBuilder()->getColumnType('chat_room_participants', 'user_id') !== 'bigint') {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            }
            
            // Add guest flag
            if (!Schema::hasColumn('chat_room_participants', 'is_guest')) {
                $table->boolean('is_guest')->default(false)->after('user_id');
            }
            
            // Update role enum to include guest
            $table->enum('role', ['customer', 'agent', 'guest'])->default('customer')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse chat_rooms changes
        Schema::table('chat_rooms', function (Blueprint $table) {
            if (Schema::hasColumn('chat_rooms', 'guest_email')) {
                $table->dropColumn('guest_email');
            }
            if (Schema::hasColumn('chat_rooms', 'guest_name')) {
                $table->dropColumn('guest_name');
            }
            if (Schema::hasColumn('chat_rooms', 'guest_session_id')) {
                $table->dropColumn('guest_session_id');
            }
            if (Schema::hasColumn('chat_rooms', 'is_guest')) {
                $table->dropColumn('is_guest');
            }
        });

        // Reverse chat_messages changes
        Schema::table('chat_messages', function (Blueprint $table) {
            if (Schema::hasColumn('chat_messages', 'is_from_guest')) {
                $table->dropColumn('is_from_guest');
            }
        });

        // Reverse chat_room_participants changes
        Schema::table('chat_room_participants', function (Blueprint $table) {
            if (Schema::hasColumn('chat_room_participants', 'is_guest')) {
                $table->dropColumn('is_guest');
            }
            // Reset role enum to original values
            $table->enum('role', ['customer', 'agent'])->default('customer')->change();
        });
    }
};
