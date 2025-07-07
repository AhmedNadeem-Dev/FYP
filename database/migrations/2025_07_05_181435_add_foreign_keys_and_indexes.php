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
        // Add indexes for better query performance
        Schema::table('products', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('category_id');
            $table->index('price');
            $table->index('created_at');
            $table->index(['user_id', 'category_id']);
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->index('product_id');
        });

        Schema::table('custom_requests', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('artist_id');
            $table->index('status');
            $table->index('created_at');
            $table->index(['status', 'created_at']);
        });

        Schema::table('custom_request_images', function (Blueprint $table) {
            $table->index('custom_request_id');
        });

        Schema::table('custom_request_comments', function (Blueprint $table) {
            $table->index('custom_request_id');
            $table->index('artist_id');
            $table->index('created_at');
        });

        Schema::table('collaborative_projects', function (Blueprint $table) {
            $table->index('creator_id');
            $table->index('status');
            $table->index('created_at');
        });

        Schema::table('project_collaborators', function (Blueprint $table) {
            $table->index('project_id');
            $table->index('user_id');
            $table->index(['project_id', 'user_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
            $table->index(['user_id', 'status']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('product_id');
            $table->index('seller_id');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('product_id');
            $table->index(['user_id', 'product_id']);
        });

        Schema::table('wishlists', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('product_id');
            $table->index(['user_id', 'product_id']);
        });

        Schema::table('portfolio_projects', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('public');
            $table->index('created_at');
            $table->index(['user_id', 'public']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('user_id');
            $table->index('created_at');
        });

        Schema::table('feedback', function (Blueprint $table) {
            $table->index('project_id');
            $table->index('user_id');
            $table->index('created_at');
        });

        Schema::table('invitations', function (Blueprint $table) {
            $table->index('project_id');
            $table->index('inviter_id');
            $table->index('invitee_id');
            $table->index('status');
            $table->index('created_at');
        });

        Schema::table('user_ratings', function (Blueprint $table) {
            $table->index('rated_user_id');
            $table->index('rating_user_id');
            $table->index('created_at');
        });

        // Add indexes to users table for better performance
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            $table->index('email');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove indexes
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['price']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['user_id', 'category_id']);
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
        });

        Schema::table('custom_requests', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['artist_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status', 'created_at']);
        });

        Schema::table('custom_request_images', function (Blueprint $table) {
            $table->dropIndex(['custom_request_id']);
        });

        Schema::table('custom_request_comments', function (Blueprint $table) {
            $table->dropIndex(['custom_request_id']);
            $table->dropIndex(['artist_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('collaborative_projects', function (Blueprint $table) {
            $table->dropIndex(['creator_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('project_collaborators', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['project_id', 'user_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['user_id', 'status']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['product_id']);
            $table->dropIndex(['seller_id']);
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['product_id']);
            $table->dropIndex(['user_id', 'product_id']);
        });

        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['product_id']);
            $table->dropIndex(['user_id', 'product_id']);
        });

        Schema::table('portfolio_projects', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['public']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['user_id', 'public']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('feedback', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('invitations', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
            $table->dropIndex(['inviter_id']);
            $table->dropIndex(['invitee_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('user_ratings', function (Blueprint $table) {
            $table->dropIndex(['rated_user_id']);
            $table->dropIndex(['rating_user_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['email']);
            $table->dropIndex(['created_at']);
        });
    }
};
