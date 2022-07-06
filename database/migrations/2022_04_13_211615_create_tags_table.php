<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag', function (Blueprint $table) {
            $table->id();
            $table->string('code', 16);
            $table->string('name');
            $table->timestamps();

            $table->index('code');
        });

        Schema::create('user_tag', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->integer('tag_id');
            $table->timestamps();

            $table->index('user_id');
            $table->index('tag_id');

            $table->foreign('user_id')->references('id')->on('user')->constrained()->onUpdate('cascade')->onDelete('cascade');
        });

        // Insert some stuff
        DB::table('tag')->insert(
            array(
                array(
                    'code' => 'unhcr',
                    'name' => 'UNHCR',
                ),
                array(
                    'code' => 'ibb',
                    'name' => 'IBB'
                ),
                array(
                    'code' => 'all',
                    'name' => 'Tümü'
                )
            )
        );

        Schema::table('term', function (Blueprint $table) {
            $table->integer('tag_id')->after('parent_id')->nullable()->default(1);
            $table->index('tag_id');
        });
        Schema::table('service', function (Blueprint $table) {
            $table->integer('tag_id')->after('location_id')->nullable()->default(1);
            $table->index('tag_id');
        });
        Schema::table('partner', function (Blueprint $table) {
            $table->integer('tag_id')->after('type_id')->nullable()->default(1);
            $table->index('tag_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tag');
    }
}
