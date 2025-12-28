<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_requests', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('model');
            $table->text('messages');
            $table->text('response')->nullable();
            $table->integer('input_tokens')->unsigned();
            $table->integer('output_tokens')->unsigned();
            $table->integer('total_tokens')->unsigned();
            $table->decimal('cost', 10, 6)->nullable();
            $table->integer('duration_ms')->unsigned()->nullable();
            $table->boolean('cached')->default(false);
            $table->timestamps();

            $table->index(['provider', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_requests');
    }
};
