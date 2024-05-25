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
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('IP', 32)->nullable();
            $table->text('inc')->nullable();
            $table->text('httpheaders')->nullable();
            $table->text('gpu')->nullable();
            $table->integer('timezone')->nullable();
            $table->text('resolution')->nullable();
            $table->text('plugins')->nullable();
            $table->text('cookie')->nullable();
            $table->text('localstorage')->nullable();
            $table->text('gpuimgs')->nullable();
            $table->text('adblock')->nullable();
            $table->integer('cpucores')->nullable();
            $table->text('canvastest')->nullable();
            $table->text('audio')->nullable();
            $table->text('langsdetected')->nullable();
            $table->text('agent')->nullable();
            $table->text('accept')->nullable();
            $table->text('encoding')->nullable();
            $table->text('language')->nullable();
            $table->text('jsFonts')->nullable();
            $table->text('WebGL')->nullable();
            $table->text('browserfingerprint')->nullable();
            $table->timestamp('time')->useCurrent();
            $table->text('label')->nullable();
            $table->text('ccaudio')->nullable();
            $table->text('hybridaudio')->nullable();
            $table->text('clientid')->nullable();
            $table->text('browser')->nullable();
            $table->text('browserid')->nullable();
            $table->text('browserversion')->nullable();
            $table->text('device')->nullable();
            $table->text('deviceid')->nullable();
            $table->text('dybrowserid')->nullable();
            $table->text('fulldevice')->nullable();
            $table->text('ipcity')->nullable();
            $table->text('ipcountry')->nullable();
            $table->text('ipregion')->nullable();
            $table->text('ispc')->nullable();
            $table->text('noipfingerprint')->nullable();
            $table->text('os')->nullable();
            $table->text('osversion')->nullable();
            $table->text('partgpu')->nullable();
            $table->text('touchSupport')->nullable();
            $table->text('doNotTrack')->nullable();
            $table->string('uniquelabel', 64)->nullable();
            $table->text('fp2_colordepth')->nullable();
            $table->text('fp2_sessionstorage')->nullable();
            $table->text('fp2_indexdb')->nullable();
            $table->text('fp2_addbehavior')->nullable();
            $table->text('fp2_opendatabase')->nullable();
            $table->text('fp2_devicememory')->nullable();
            $table->text('fp2_cpuclass')->nullable();
            $table->text('fp2_pixelratio')->nullable();
            $table->text('fp2_liedlanguages')->nullable();
            $table->text('fp2_liedresolution')->nullable();
            $table->text('fp2_liedos')->nullable();
            $table->text('fp2_liedbrowser')->nullable();
            $table->text('fp2_webgl')->nullable();
            $table->text('fp2_webglvendoe')->nullable();
            $table->text('fp2_platform')->nullable();
            $table->text('fp2_liedlanguagesdetails')->nullable();
            $table->text('fp2_liedresolutiondetails')->nullable();
            $table->text('fp2_liedosdetails')->nullable();
            $table->text('fp2_liedbrowserdetails')->nullable();
            $table->text('iplocation')->nullable();

            $table->unique('uniquelabel');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};
