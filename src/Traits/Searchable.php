<?php

namespace Novacio\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Searchable trait
 *
 * @property-read string[] $searchable
 */
trait Searchable
{
    protected function fullTextWildcards($term): string
    {
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
        $term = str_replace($reservedSymbols, '', $term);

        $words = explode(' ', $term);

        foreach ($words as $key => $word) {
            /**
             * applying + operator (required word) only big words
             * because smaller ones are not indexed by mysql
             */
            if (strlen($word) >= 3) {
                $words[$key] = "+{$word}*";
            }
        }

        $searchTerm = implode(' ', $words);

        return $searchTerm;
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $columns = implode(',', $this->searchable);
        $mode = config('app.search');
        $query->selectRaw("*, MATCH ({$columns}) AGAINST (? IN {$mode}) as score", [$this->fullTextWildcards($term)])->whereRaw("MATCH ({$columns}) AGAINST (? IN {$mode})", $this->fullTextWildcards($term))->orderby('score', 'DESC');

        return $query;
    }
}
