<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class MacrosProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Builder::macro('if', function ($condition, $column, $operator, $value) {
            if ($condition) {
                return $this->where($column, $operator, $value);
            }

            return $this;
        });
        Builder::macro('notEmptyOrWhere', function ($column, $param) {
            $this->when(!empty($param), function (Builder $query) use ($column, $param) {
                return $query->orWhere($column, $param);
            });

            return $this;
        });
        Builder::macro('notEmptyOrWhereIn', function ($column, $params) {
            $this->when(!empty($params), function (Builder $query) use ($column, $params) {
                return $query->orWhereIn($column, Arr::wrap($params));
            });

            return $this;
        });

        Builder::macro('notEmptyOrWhereNotIn', function ($column, $params) {
            $this->when(!empty($params), function (Builder $query) use ($column, $params) {
                return $query->orWhereNotIn($column, Arr::wrap($params));
            });

            return $this;
        });
        Builder::macro('notEmptyWhere', function ($column, $param) {
            $this->when(!empty($param), function (Builder $query) use ($column, $param) {
                return $query->where($column, $param);
            });

            return $this;
        });
        Builder::macro('notEmptyWhereDate', function ($column, $param) {
            $this->when(!empty($param), function (Builder $query) use ($column, $param) {
                return $query->whereDate($column, $param);
            });

            return $this;
        });
        Builder::macro('notEmptyWhereDay', function ($column, $param) {
            $this->when(!empty($param), function (Builder $query) use ($column, $param) {
                return $query->whereDay($column, $param);
            });

            return $this;
        });
        Builder::macro('notEmptyWhereIn', function ($column, $params) {
            $this->when(!empty($params), function (Builder $query) use ($column, $params) {
                return $query->whereIn($column, Arr::wrap($params));
            });

            return $this;
        });
        Builder::macro('notEmptyWhereMonth', function ($column, $param) {
            $this->when(!empty($param), function (Builder $query) use ($column, $param) {
                return $query->whereMonth($column, $param);
            });

            return $this;
        });
        Builder::macro('notEmptyWhereNotIn', function ($column, $params) {
            $this->when(!empty($params), function (Builder $query) use ($column, $params) {
                return $query->whereNotIn($column, Arr::wrap($params));
            });

            return $this;
        });
        Builder::macro('notEmptyWhereTime', function ($column, $param) {
            $this->when(!empty($param), function (Builder $query) use ($column, $param) {
                return $query->whereTime($column, $param);
            });

            return $this;
        });
        Builder::macro('notEmptyWhereYear', function ($column, $param) {
            $this->when(!empty($param), function (Builder $query) use ($column, $param) {
                return $query->whereYear($column, $param);
            });

            return $this;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
