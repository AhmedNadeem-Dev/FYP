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
        // Fix products table - change primary key to match DBML
        Schema::table('products', function (Blueprint $table) {
            $table->dropPrimary();
            $table->renameColumn('id', 'product_id');
            $table->primary('product_id');
        });

        // Fix categories table - ensure proper primary key
        if (!Schema::hasColumn('categories', 'category_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropPrimary();
                $table->renameColumn('id', 'category_id');
                $table->primary('category_id');
            });
        }

        // Add missing columns to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'total_rating')) {
                $table->decimal('total_rating', 3, 2)->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'rating_count')) {
                $table->integer('rating_count')->default(0)->after('total_rating');
            }
        });

        // Fix carts table references
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
        });

        // Fix wishlists table references
        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
        });

        // Fix order_items table references
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
        });

        // Fix comments table references
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
        });

        // Fix product_images table references
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Reverse the changes
        Schema::table('products', function (Blueprint $table) {
            $table->dropPrimary();
            $table->renameColumn('product_id', 'id');
            $table->primary('id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropPrimary();
            $table->renameColumn('category_id', 'id');
            $table->primary('id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['total_rating', 'rating_count']);
        });
    }
};
