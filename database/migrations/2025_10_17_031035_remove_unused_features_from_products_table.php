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
        Schema::table('products', function (Blueprint $table) {
            // Check and drop columns if they exist
            $columns = [
                'author_id',
                'book_file',
                'isbn',
                'edition',
                'pages',
                'country',
                'language',
                'book',
                'download_able',
                'download_limit',
                'download_expire',
                'whole_price',
                'point',
                'type',
                'sheba',
                'video',
                'video_thumb',
                'videoTName',
                'yvideo',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('author_id')->nullable()->after('brand_id');
            $table->string('book_file')->nullable();
            $table->string('isbn')->nullable();
            $table->string('edition')->nullable();
            $table->integer('pages')->nullable();
            $table->string('country')->nullable();
            $table->string('language')->nullable();
            $table->boolean('book')->default(false);
            $table->boolean('download_able')->default(false);
            $table->integer('download_limit')->nullable();
            $table->date('download_expire')->nullable();
            $table->decimal('whole_price', 10, 2)->nullable();
            $table->decimal('point', 10, 2)->nullable();
            $table->tinyInteger('type')->default(0);
            $table->boolean('sheba')->default(false);
            $table->string('video')->nullable();
            $table->string('video_thumb')->nullable();
            $table->string('videoTName')->nullable();
            $table->text('yvideo')->nullable();
        });
    }
};