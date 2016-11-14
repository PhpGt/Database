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

public function prepare():QueryInterface;

}#