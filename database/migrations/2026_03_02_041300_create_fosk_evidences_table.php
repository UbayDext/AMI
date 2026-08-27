<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fosk_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('fosk_documents')->cascadeOnDelete();
            $table->string('title');
            $table->string('link_url')->nullable();       // link ke GDrive / website
            $table->string('file_path')->nullable();      // file upload lokal
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fosk_evidences');
    }
};
