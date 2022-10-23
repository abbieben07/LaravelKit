<?php

namespace Novacio\Custom;

use Illuminate\Database\Eloquent\Builder as Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Select2
{
    public static function toSelect2(Model|Collection $object, Request $request): JsonResponse
    {
        $offset = $request["offset"];
        $number = $request["number"] ?? 10;
        $search = $request["search"];

        $more = $object->get()->count() > ($offset + $number);
        $model = $object;

        if (!empty($search)) {
            $model->search($search);
        }

        if (!empty($where)) {
            foreach ($where as $field => $condition) {
                if (is_array($condition)) {
                    $model->whereIn($field, $condition);
                } else {
                    $model->where($field, $condition);
                }
            }
        }

        $objects = $model->take($number)->skip($offset)->get();
        $data = [];
        if (!empty($attributes)) {
            $data = [];

            foreach ($objects as $key => $object) {
                foreach ($attributes as $index => $attribute) {
                    if (is_string($index) && method_exists($object, $index)) {
                        $data[$key][$index] = $object->$index($attribute);
                    } elseif (method_exists($object, $attribute)) {
                        $data[$key][$attribute] = $object->$attribute();
                    } else {
                        data_set($data[$key], $attribute, data_get($object, $attribute));
                    }
                }
            }
        } else {
            $data = $objects;
        }

        $collection = collect($data->toArray())->transform(
            function ($item) {
                $item["id"] = $item["slug"];

                return $item;
            }
        );

        $response = [
            "results" => $collection,
            "pagination" => [
                "more" => $more,
            ],
        ];

        return response()->json($response);
    }
}
