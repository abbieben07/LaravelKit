<?php

namespace Novacio\Custom;

use Illuminate\Database\Eloquent\Builder as Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DataTable
{
    public static function toDataTable(Model|Collection $object, Request $request): JsonResponse
    {
        //$user = $request->user();
        $search = $request["search"]["value"];
        $offset = $request["start"];
        $number = $request["length"];
        $orderable = $request["orderable"];
        $order = $request["order"][0]["dir"];
        $filter = $request["filter"];
        $trashed = $request["trash"] === "true";
        $type = isset($filter["type"]) ? $filter["type"] : null;

        if ($object instanceof Model) {
            /** @var Model $model */
            $model = $object;
            $total = $model->get()->count();

            $model->when($search, fn () => $model->search($search))
                ->when($trashed, fn () => $model->onlyTrashed())
                ->when($orderable, function ($query, $orderable) use ($model, $request) {
                    $orders = $request["order"];
                    foreach ($orders as $order) {
                        $dir = $order["dir"];
                        $column = $request["columns"][$order["column"]]["name"];
                        if (in_array($column, $model->columns())) {
                            $model->orderby($column, $dir);
                        }
                    }
                });

            $categories = [];
            if (isset($type)) {
                if (array_key_exists($type, $model->attributes())) {
                    $categories = $model->get()->map(fn ($item) => ["title" => data_get($item->toArray(), $type), "id" => data_get($item->toArray(), $type)])->unique("id")->values()->toArray();
                } else {
                    $categories = $model->get()->load($type)->map(fn ($item) => ["title" => data_get($item->toArray(), "{$type}.label"), "id" => data_get($item->toArray(), "{$type}.id")])->unique("id")->values()->toArray();
                }
            }
            if (isset($filter["type"]) && isset($filter["value"])) {
                $model->whereHas($filter["type"], fn (Model $query) => $query->whereId($filter["value"]));
            }
            if (!empty($where)) {
                foreach ($where as $column => $value) {
                    $model->where($column, $value);
                }
            }

            $filtered = $model->get()->count();

            $attributes = data_get($request, "columns.*.data");
            $attributes[] = "id";
            //$attributes[] = "model";
            $data = $model->take($number)->skip($offset)->get();

            if (isset($attributes)) {
                //$data = $data->map->only($attributes);
                $objects = $data;
                $data = [];

                foreach ($objects as $key => $object) {
                    foreach ($attributes as $attribute) {
                        data_set($data[$key], $attribute, data_get($object, $attribute));
                    }
                }
            }
        }

        $response = [
            "draw" => $request->draw,
            "data" => $data,
            "recordsFiltered" => $filtered,
            "recordsTotal" => $total,
            "categories" => $categories,
        ];

        return response()->json($response);
    }

    public static function toEditor(Model $object, Request $request, $action): JsonResponse
    {
        if ($request["action"] == "remove") {
            $result = [];
            foreach ($request["data"] as $key => $row) {
                $object = $object->withTrashed()->find($key);
                if (!$object->trashed()) {
                    $object->delete();
                    if ($object->trashed()) {
                        $result[] = $object->id;
                    }
                } else {
                    $object->restore();
                    if (!$object->trashed()) {
                        $result[] = $object->id;
                    }
                }
            }

            return $result;
        } elseif ($request["action"] == "edit") {
            $result = [];
            foreach ($request["data"] as $key => $row) {
                $object = $object->find($key);
                if (property_exists($object, $action)) {
                    foreach ($row as $index => $data) {
                        return $object->{$index}; //= $data;
                    }
                    $object->save();
                }
                $result[] = $object->id;
            }

            return $result;
        }
    }
}
