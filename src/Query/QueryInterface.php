<?php
namespace Gt\Database\Query;

/**
 * Represents either an SQL file containing the raw query, or PHP file
 * containing the \Gt\Database\Query\BuilderInterface class.
 *
 * SQL will be read in using \Illuminate\Database\Query\Builder::raw whereas
 * a PHP BuilderInterface object will directly manipulate this object using
 * any public methods provided by \Illuminate\Database\Query\Builder.
 *
 * @see \Illuminate\Database\Query\Builder
 */
interface QueryInterface {

// TODO: PHP 7.1 iterable, to allow Gt\Database\Gt\Database\PlaceholderMap

// TODO: Does the \Illuminate\Database\Query\Builder get passed in through a
// method? If so, what interface does _that_ class respect?

}#