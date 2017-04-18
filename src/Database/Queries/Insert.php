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
     * @param Database     $database
     */
    public function __construct(Database $database, $connection)
    {
        parent::__construct($database->queryBuilder($connection), $connection);
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
            $database->queryInstruction($this, $this->connection);

            return $builder->adapter()->insertId();
        }, $this->connection);
    }

}