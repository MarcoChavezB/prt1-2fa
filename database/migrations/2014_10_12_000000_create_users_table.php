<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->string('verification_code')->nullable();
            $table->string('verification_code_expires_at')->nullable();

            $table->string('two_factor_code')->nullable();
            $table->string('two_factor_code_expires_at')->nullable();
            $table->boolean('two_factor_verified')->default(false);

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
¡Un viaje inolvidable te espera!   Descubre la emoción de volar por los cielos en nuestro simulador. Una experiencia única que te dejará sin aliento. ¡Siente la adrenalina de un verdadero piloto y despega hacia nuevas aventuras! ✈️ #simuladordevuelo #aviacion #pilot #vuelo #AviatrainingandTechnology

     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
