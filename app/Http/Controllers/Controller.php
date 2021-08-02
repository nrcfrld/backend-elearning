<?php

namespace App\Http\Controllers;

use Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Specialtactics\L5Api\Http\Controllers\RestfulController as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static $searchableFields = [];

    public function ordering($query, $model)
    {
        /*
            orderBy harus berupa Array
            index pertama adalah field
            index kedua adalah sortType(desc atau asc)
        */
        if(request()->has('order_by')){
            $tableName = new $model();
            $tableName = $tableName->getTable();
            $orderBy = request('order_by');
            if(Schema::hasColumn($tableName, $orderBy[0])){
                $query->orderBy($orderBy[0], $orderBy[1]);
            }
        }
    }

    public function filter($query, $model)
    {
        /*
            kirimkan request dengan key pada table dan value adalah untuk filter
            contoh: name=Enrico
            akan mencari data dengan name yang sesuai dengan Enrico
        */
        $tableName = new $model();
        $tableName = $tableName->getTable();
        foreach (request()->all() as $key => $value) {
            if (Schema::hasColumn($tableName, $key)) {
                $query->where($key, $value);
            }
        }
    }

    public function getAll()
    {
        $this->authorizeUserAction('viewAll');

        $model = new static::$model;

        // If we are caching the endpont, do a simple get all resources
        if (static::$cacheAll) {
            return $this->response->collection(Cache::remember(static::getCacheKey(), static::$cacheExpiresIn, function () use ($model) {
                return $model::with($model::getCollectionWith())->get();
            }), $this->getTransformer());
        }

        $query = $model::with($model::getCollectionWith());
        $this->queryIndex($query);
        $this->qualifyCollectionQuery($query);
        $this->filter($query, $model);
        $this->ordering($query, $model);

        // Handle pagination, if applicable
        $perPage = $model->getPerPage();
        if ($perPage) {
            // If specified, use per_page of the request
            if (request()->has('per_page')) {
                $perPage = intval(request()->input('per_page'));
            }

            $paginator = $query->paginate($perPage);

            return $this->response->paginator($paginator, $this->getTransformer());
        } else {
            $resources = $query->get();

            return $this->response->collection($resources, $this->getTransformer());
        }
    }

    public function queryIndex(&$query)
    {
        return $query;
    }
}
