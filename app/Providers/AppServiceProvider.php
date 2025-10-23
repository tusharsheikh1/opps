<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Order;
use App\Observers\OrderObserver;
use App\Models\Collection;
use App\Models\Category;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register Order Observer for SMS functionality
        Order::observe(OrderObserver::class);

        // --- UPDATED THIS BLOCK WITH CORRECT RELATIONSHIPS ---
        // Share active collections and categories with the header
        // IMPORTANT: This path must match where your header file is located.
        View::composer('layouts.frontend.partials.header_1', function ($view) {
            $collections = Collection::where('status', true)->latest('id')->get();
            
            // Fetch categories with all their nested relationships
            // Using correct foreign keys:
            // Category -> SubCategory (category_id)
            // SubCategory -> miniCategory (sub_category_id) 
            // miniCategory -> ExtraMiniCategory (mini_category_id)
            $categories = Category::where('status', true)
                ->orderBy('pos', 'asc')
                ->with([
                    'sub_categories' => function($query) {
                        $query->where('status', true)
                              ->orderBy('id', 'asc')
                              ->with([
                                  'miniCategory' => function($q) {
                                      $q->where('status', true)
                                        ->orderBy('id', 'asc')
                                        ->with([
                                            'extraCategory' => function($qq) {
                                                $qq->where('status', true)
                                                   ->orderBy('id', 'asc');
                                            }
                                        ]);
                                  }
                              ]);
                    }
                ])
                ->get();
            
            $view->with('collections', $collections);
            $view->with('categories', $categories);
        });
        // --- END OF UPDATED BLOCK ---


        Builder::macro('filter', function($key, $column = null, $compareWith = null, $filterIf = true) {
            if(($value = request($key, null)) !== null && $filterIf) {
                return $this->where($column ?? $key, $compareWith ?? '=', $value);
            }
            return $this;
        });

        Builder::macro('filterWith', function($key, $column = null) {
            if((request($key, null)) !== null) {
                $value = request($key, null);
                return $this->whereIn($column ?? $key, $value);
            }
            return $this;
        });

        
        Builder::macro('whereLike', function ($attributes, string $searchTerm) {
            $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach ($attributes as $attribute) {
                    $query->when(
                        str_contains($attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm) {
                            [$relationName, $relationAttribute] = explode('.', $attribute);
        
                            $query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm) {
                                $query->where($relationAttribute, 'LIKE', "%{$searchTerm}%");
                            });
                        },
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            });
        
            return $this;
        });
    }
}