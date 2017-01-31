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
        $this->database->queryInstruction($this);

        return $this->builder->adapter()->insertId();
    }

}