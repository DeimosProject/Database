<?php

namespace Deimos\Database\Queries;

use Deimos\Database\Database;
use Deimos\QueryBuilder\Instruction;
use Deimos\QueryBuilder\QueryBuilder;

class Insert extends Instruction\Insert
{

    /**
     * @var Database
     */
    protected $database;

    /**
     * Instruction constructor.
     *
     * @param QueryBuilder $builder
     * @param Database     $database
     */
    public function __construct(QueryBuilder $builder, Database $database)
    {
        parent::__construct($builder);
        $this->database = $database;
    }

    /**
     * @return int
     */
    public function insert()
    {
        $builder = $this->builder;

        return $this->database->transaction()->call(function ($database) use ($builder)
        {
            /**
             * @var Database     $database
             * @var QueryBuilder $builder
             */
            $database->queryInstruction($this);

            return $builder->adapter()->insertId();
        });
    }

}