<?php

namespace App\Console\Commands;

use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class TestWhatsAppCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wa:test {phone? : Nomor WhatsApp Tujuan (contoh: 081234567890)} {message? : Pesan Uji Coba}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uji coba pengiriman pesan WhatsApp via Baileys Bot / Gateway';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $phone = $this->argument('phone') ?: $this->ask('Masukkan Nomor WhatsApp Tujuan (contoh: 081234567890)');
        $message = $this->argument('message') ?: $this->ask('Masukkan Pesan Uji Coba', 'Halo! Ini adalah pesan tes notifikasi WhatsApp dari Eskasaba Marketplace.');

        $this->info("Mengirim pesan ke: {$phone}...");
        $this->info("Isi Pesan: \"{$message}\"");

        $success = WhatsAppService::send($phone, $message);

        if ($success) {
            $this->output->success("✅ Pesan WhatsApp BERHASIL dikirim!");
            return Command::SUCCESS;
        }

        $this->output->error("❌ Pesan WhatsApp GAGAL dikirim. Pastikan server Baileys bot (node index.js) sudah aktif di port 3000 dan QR Code telah di-scan.");
        return Command::FAILURE;
    }
}
