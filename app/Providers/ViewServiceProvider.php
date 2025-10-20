<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Page;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\miniCategory;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Using a view composer to share data with all views ('*')
        // This closure will only be executed when a view is rendered.
        View::composer('*', function ($view) {
            
            // We check if the database has been migrated to prevent errors during artisan commands
            if (Schema::hasTable('pages') && Schema::hasTable('categories') && Schema::hasTable('sub_categories') && Schema::hasTable('mini_categories')) {
                $footerPages = Page::where('status', '1')->where('position', '1')->get();
                $categories_f = Category::where('status', true)->where('is_feature', '1')->orderBy('updated_at', 'desc')->take(10)->get();
                $sub_f = SubCategory::where('status', true)->where('is_feature', '1')->orderBy('updated_at', 'desc')->take(10)->get();
                $mini_f = miniCategory::where('status', true)->where('is_feature', '1')->orderBy('updated_at', 'desc')->take(10)->get();

                $view->with([
                    'footerPages' => $footerPages,
                    'categories_f' => $categories_f,
                    'sub_f' => $sub_f,
                    'mini_f' => $mini_f,
                ]);

            } else {
                // If tables don't exist, share empty collections to avoid errors in views
                 $view->with([
                    'footerPages' => collect(),
                    'categories_f' => collect(),
                    'sub_f' => collect(),
                    'mini_f' => collect(),
                ]);
            }
        });
    }
}