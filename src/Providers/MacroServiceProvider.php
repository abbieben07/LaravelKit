<?php

namespace Novacio\Providers;

use Facebook\WebDriver\Exception\ElementNotInteractableException;
use Facebook\WebDriver\Exception\WebDriverException;
use Illuminate\Database\Eloquent\Builder as Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Novacio\Custom\DataTable;
use Novacio\Custom\Select2;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register the application"s all macros.
     *
     * @return void
     */
    public function boot()
    {
        Str::macro("randomInt", fn ($count) => rand(pow(10, $count - 1), pow(10, $count) - 1));
        Str::macro("similar", fn ($a, $b) => Str::lower($a) == Str::lower($b));

        //================================================================
        // Route Blueprint Modifications s
        //================================================================
        Route::macro("plural", function () {
            /** @var Route $this */
            Str::of("Mango")->plural();
        });

        //================================================================
        // Request Blueprint Modifications
        //================================================================
        Request::macro("active", function ($route) {
            /** @var Request $this */
            return $this->route()->named($route) ? "active" : "";
        });

        //================================================================
        // Collections Modifications
        //================================================================
        Collection::macro("sumMoney", function ($column) {
            /** @var Collection $this */
            return $this->sum(fn ($data) => $data->{$column}->getAmount());
        });

        Collection::macro("members", function () {
            /** @var Collection $this */
            return $this->pluck("members")->flatten();
        });

        Collection::macro("toDatatable", function (Request $request, $attributes, $where = []) {
            /** @var Collection $this */
            $search = $request["search"]["value"];
            $offset = $request["start"];
            $number = $request["length"];
            $orderable = $request->query("orderable");
            $orderby = $request["columns"][$request["order"][0]["column"]]["name"];
            $order = $request["order"][0]["dir"];
            $filter = $request["filter"] ?? "all";
            $trashed = $request["trash"] === "true";

            $collection = $this;
            $total = $this->count();

            if (!empty($search)) {
                $collection = $collection->search($search);
            }

            if ($trashed) {
                $collection = $collection->onlyTrashed();
            }

            if ($orderable) {
                $collection = $collection->orderby($orderby, $order);
            }

            if ($filter !== "all") {
                $collection = $collection->where("", $filter);
            }

            if (!empty($where)) {
                foreach ($where as $field => $condition) {
                    $collection->where($field, $condition);
                }
            }

            $filtered = $collection->count();
            $objects = $collection->map->get($attributes)->take($number)->skip($offset);

            $data = [];

            foreach ($objects as $key => $object) {
                foreach ($attributes as $index => $attribute) {
                    if (is_string($index) && method_exists($object, $index)) {
                        $data[$key][$index] = $object->$attribute($attribute);
                    } elseif (method_exists($object, $attribute)) {
                        $data[$key][$attribute] = $object->$attribute();
                    } else {
                        data_set($data[$key], $attribute, data_get($object, $attribute));
                    }
                }
            }

            $result = ["data" => $data, "recordsFiltered" => $filtered, "recordsTotal" => $total];

            return response()->json($result);
        });

        Collection::macro("props", function ($attributes) {
            /** @var Collection $this */
            return $this->map(
                function ($item) use ($attributes) {
                    $result = [];
                    foreach ($attributes as $attribute) {
                        data_set($result, $attribute, data_get($item, $attribute));
                    }

                    return $result;
                }
            );
        });

        Collection::macro("paginate", function ($perPage, $page = null, $total = null, $pageName = "page") {
            /** @var Collection $this */
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    "path" => LengthAwarePaginator::resolveCurrentPath(),
                    "pageName" => $pageName,
                ]
            );
        });

        //================================================================
        // Model Modifications
        //================================================================

        Model::macro("columns", function () {
            /** @var Model $this */
            return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getModel()->getTable());
        });

        /**
         * @mixin Model
         *
         * @method JsonResponse toDataTable(Request $request)
         */
        Model::macro("toDatatable", function (Request $request): JsonResponse {
            /** @var Model $this */
            return DataTable::toDataTable($this, $request);
        });

        /**
         * @mixin Model
         *
         * @method toEditor()
         */
        Model::macro("toEditor", function (Request $request, $action = null): JsonResponse {
            /** @var Model $this */
            return DataTable::toEditor($this, $request, $action);
        });

        /**
         * @mixin Model
         *
         * @method toSelect2(Request $request)
         */
        Model::macro("toSelect2", function (Request $request): JsonResponse {
            /** @var Model $this */
            return Select2::toSelect2($this, $request);
        });

        Model::macro("clean", function () {
            /** @var Model $this */
            foreach ($this->getModel()->getAttributes() as $key => $attribute) {
                if (!in_array(($key), $this->columns())) {
                    unset($this->getModel()->$key);
                }
            }
        });

        if (\class_exists(\Laravel\Dusk\Browser::class)) {
            \Laravel\Dusk\Browser::macro("select2", function ($field, $value = null, $wait = 2, $suffix = " + .select2") {
                /** @var Browser $this */
                $prefix = $this->resolver->prefix;
                $selector = $field . $suffix;
                $element = $this->element($selector);

                if (!$element && !$element?->isDisplayed()) {
                    throw new InvalidArgumentException("Selector [$selector] not found or not displayed.");
                }

                $container = ".select2-container";
                $highlightedClass = ".select2-results__option--highlighted";
                $highlightedSelector = ".select2-results__options " . $highlightedClass;
                $selectableSelector = ".select2-results__options .select2-results__option--selectable";
                $searchSelector = "{$container} .select2-search__field";

                $this->click($selector);

                // if $value equal null, find random element and click him.
                // @todo: may be a couple of times move scroll to down (ajax paging)
                if (null === $value) {
                    //$this->pause($wait * 1000);
                    $this->resolver->prefix = "";
                    $this->waitFor($selectableSelector, $wait);
                    $options = $this->elements($selectableSelector);
                    $count = 1; //$this->attribute($field, "multiple") ? random_int(1, count($options)) : 1;

                    for ($i = 0; $i < $count; $i++) {
                        /** @var RemoteWebElement $option */
                        $option = $options[array_rand($options, 1)];
                        //dump()
                        $option->click();
                    }

                    return $this;
                }

                // check if search field exists and fill it.
                $element = $this->element($searchSelector);

                if ($element?->isDisplayed()) {
                    try {
                        foreach ((array) $value as $item) {
                            $element->sendKeys($item);
                            $this->waitFor($highlightedSelector, $wait);
                            $this->click($highlightedSelector);
                        }

                        return $this;
                    } catch (WebDriverException $exception) {
                        if (!$exception instanceof ElementNotInteractableException) {
                            throw $exception;
                        }
                        // ... otherwise ignore the exception and try another way
                    }
                }

                // another way - w/o search field.
                //$field = str_replace("\\", "\\\\", $field);
                // $this->script("jQuery(\"$field\").val((function () { return jQuery(\"$field option:contains("$value")\").val(); })()).trigger("change")");

                //$this->click($selector);
                //$this->waitUntilMissing(".select2-results__options");

                return $this;
            });
        }
    }
}
