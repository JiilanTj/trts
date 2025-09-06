<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\ChatRoomParticipant;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create test users
        $customer = User::firstOrCreate(
            ['username' => 'customer'],
            [
                'full_name' => 'Test Customer',
                'role' => 'user',
                'password' => bcrypt('password'),
                'level' => 1,
                'credit_score' => 100,
                'is_seller' => false,
            ]
        );

        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'full_name' => 'Test Admin',
                'role' => 'admin',
                'password' => bcrypt('password'),
                'level' => 1,
                'credit_score' => 100,
                'is_seller' => false,
            ]
        );

        // Create test chat rooms
        $chatRoom1 = ChatRoom::create([
            'user_id' => $customer->id,
            'admin_id' => $admin->id,
            'subject' => 'Bantuan Pembayaran',
            'priority' => 'medium',
            'status' => 'assigned',
            'last_message_at' => now(),
        ]);

        $chatRoom2 = ChatRoom::create([
            'user_id' => $customer->id,
            'subject' => 'Masalah Produk',
            'priority' => 'high',
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        $chatRoom3 = ChatRoom::create([
            'user_id' => $customer->id,
            'admin_id' => $admin->id,
            'subject' => 'Pertanyaan Umum',
            'priority' => 'low',
            'status' => 'closed',
            'last_message_at' => now()->subDays(1),
            'closed_at' => now()->subHours(2),
        ]);

        // Add participants
        foreach ([$chatRoom1, $chatRoom2, $chatRoom3] as $room) {
            // Add customer as participant
            ChatRoomParticipant::create([
                'chat_room_id' => $room->id,
                'user_id' => $customer->id,
                'role' => 'customer',
                'joined_at' => $room->created_at,
            ]);

            // Add admin as participant (if assigned)
            if ($room->admin_id) {
                ChatRoomParticipant::create([
                    'chat_room_id' => $room->id,
                    'user_id' => $room->admin_id,
                    'role' => 'agent',
                    'joined_at' => $room->created_at->addMinutes(5),
                ]);
            }
        }

        // Create test messages
        $messages = [
            // Chat Room 1 messages
            [
                'chat_room_id' => $chatRoom1->id,
                'user_id' => $customer->id,
                'message' => 'Halo, saya butuh bantuan dengan pembayaran untuk order saya.',
                'message_type' => 'text',
                'created_at' => now()->subMinutes(30),
                'updated_at' => now()->subMinutes(30),
            ],
            [
                'chat_room_id' => $chatRoom1->id,
                'user_id' => $admin->id,
                'message' => 'Selamat datang! Saya akan membantu Anda. Bisa tolong berikan nomor order Anda?',
                'message_type' => 'text',
                'is_read' => true,
                'read_at' => now()->subMinutes(25),
                'created_at' => now()->subMinutes(25),
                'updated_at' => now()->subMinutes(25),
            ],
            [
                'chat_room_id' => $chatRoom1->id,
                'user_id' => $customer->id,
                'message' => 'Nomor order saya adalah #ORD-001234. Pembayaran sudah saya transfer tapi status belum berubah.',
                'message_type' => 'text',
                'created_at' => now()->subMinutes(20),
                'updated_at' => now()->subMinutes(20),
            ],
            [
                'chat_room_id' => $chatRoom1->id,
                'user_id' => $admin->id,
                'message' => 'Baik, saya cek dulu ya. Biasanya membutuhkan waktu 1-2 jam untuk konfirmasi pembayaran.',
                'message_type' => 'text',
                'created_at' => now()->subMinutes(15),
                'updated_at' => now()->subMinutes(15),
            ],

            // Chat Room 2 messages
            [
                'chat_room_id' => $chatRoom2->id,
                'user_id' => $customer->id,
                'message' => 'Produk yang saya terima tidak sesuai dengan deskripsi. Bagaimana cara returnya?',
                'message_type' => 'text',
                'created_at' => now()->subMinutes(10),
                'updated_at' => now()->subMinutes(10),
            ],

            // Chat Room 3 messages (closed)
            [
                'chat_room_id' => $chatRoom3->id,
                'user_id' => $customer->id,
                'message' => 'Bagaimana cara menjadi seller di platform ini?',
                'message_type' => 'text',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'chat_room_id' => $chatRoom3->id,
                'user_id' => $admin->id,
                'message' => 'Untuk menjadi seller, Anda perlu melengkapi verifikasi KYC dulu, kemudian ajukan permohonan di menu Profile > Seller Request.',
                'message_type' => 'text',
                'is_read' => true,
                'read_at' => now()->subDays(1)->addMinutes(10),
                'created_at' => now()->subDays(1)->addMinutes(5),
                'updated_at' => now()->subDays(1)->addMinutes(5),
            ],
            [
                'chat_room_id' => $chatRoom3->id,
                'user_id' => $customer->id,
                'message' => 'Baik, terima kasih informasinya!',
                'message_type' => 'text',
                'is_read' => true,
                'read_at' => now()->subDays(1)->addMinutes(15),
                'created_at' => now()->subDays(1)->addMinutes(12),
                'updated_at' => now()->subDays(1)->addMinutes(12),
            ],
        ];

        foreach ($messages as $messageData) {
            ChatMessage::create($messageData);
        }

        $this->command->info('Chat test data created successfully!');
        $this->command->info("Customer login: customer / password");
        $this->command->info("Admin login: admin / password");
        $this->command->info("Created {$chatRoom1->id}, {$chatRoom2->id}, {$chatRoom3->id} chat rooms");
    }
}
