<?php

namespace Deimos\Database\Queries;

use Deimos\Database\Database;
use Deimos\QueryBuilder\Instruction;
use Deimos\QueryBuilder\QueryBuilder;

class Delete extends Instruction\Delete
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
    public function __construct(Database $database)
    {
        parent::__construct($database->queryBuilder());
        $this->database = $database;
    }

    /**
     * @return int
     */
    public function delete()
    {
        $query = $this;

        return $this->database->transaction()->call(function ($database) use ($query)
        {
            /**
             * @var Database     $database
             */
            return $database
                ->queryInstruction($query)
                ->rowCount();
        });
    }

}