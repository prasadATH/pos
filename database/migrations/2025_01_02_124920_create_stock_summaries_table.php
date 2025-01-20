<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateStockSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('product_name');
            $table->integer('received')->default(0);
            $table->integer('daily_franchise')->default(0);
            $table->integer('retail')->default(0);
            $table->integer('courier_transfer')->default(0);
            $table->integer('pending')->default(0);
            $table->integer('meeting_order')->default(0);
            $table->integer('staff_issued')->default(0);
            $table->integer('director_issued')->default(0);
            $table->integer('return')->default(0);
            $table->integer('promotion')->default(0);
            $table->integer('sample')->default(0);
            $table->integer('damaged')->default(0);
            $table->integer('total_stocks_remaining')->default(0);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        // Insert all products from the products table
        $this->insertProductsFromProductsTable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_summaries');
    }

    /**
     * Insert all products from the products table into the stocks table.
     *
     * @return void
     */
    private function insertProductsFromProductsTable()
    {
        $products = DB::table('products')->select('id', 'name')->get();

        foreach ($products as $product) {
            DB::table('stock_summaries')->insert([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'received' => 0,
                'daily_franchise' => 0,
                'retail' => 0,
                'courier_transfer' => 0,
                'pending' => 0,
                'meeting_order' => 0,
                'staff_issued' => 0,
                'director_issued' => 0,
                'return' => 0,
                'promotion' => 0,
                'sample' => 0,
                'damaged' => 0,
                'total_stocks_remaining' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
