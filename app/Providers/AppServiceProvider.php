<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Order;
use App\Observers\OrderObserver;
use App\Models\Collection; // Added this line
use Illuminate\Support\Facades\View; // Added this line

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

        // --- ADDED THIS BLOCK ---
        // Share active collections with the header
        // IMPORTANT: This path must match where your header file is located.
        View::composer('layouts.frontend.partials.header_1', function ($view) {
            $view->with('collections', Collection::where('status', true)->latest('id')->get());
        });
        // --- END OF ADDED BLOCK ---


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