<?php

namespace Novacio\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Routes trait
 *
 * @property-read string $model
 * @property-read string[] $url
 */
trait Routes
{
    public function url(): Attribute
    {
        return Attribute::make(get: function () {
            $single = route("{$this->model}.page.single", ["{$this->model}" => $this]);
            $update = route("{$this->model}.page.update", ["{$this->model}" => $this]);
            $delete = route("{$this->model}.page.delete", ["{$this->model}" => $this]);

            $url = ['single' => $single, 'update' => $update, 'delete' => $delete];

            return $url;
        });
    }

    public function model(): Attribute
    {
        return Attribute::make(
            get: fn () => strtolower(basename(self::class))
        );
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('slug', $value)->firstOrFail();
    }

    public function resolveSoftDeletableRouteBinding($value, $field = null)
    {
        return parent::resolveSoftDeletableRouteBinding($value, $field);
    }

    /*  public function resolveChildRouteBinding($childType, $value, $field)
    {
        return parent::resolveChildRouteBinding($childType, $value, $field);
    } */
}
