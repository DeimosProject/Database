<?php

namespace Deimos\Database\Queries;

use Deimos\Database\Database;
use Deimos\QueryBuilder\Instruction;
use Deimos\QueryBuilder\QueryBuilder;

class Update extends Instruction\Update
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
     * @param Update|null $query
     *
     * @return int
     */
    public function update(self $query = null)
    {
        $query = $query ?: $this;

        return $this->database->transaction()->call(function ($database) use ($query)
        {
            /**
             * @var Database $database
             */
            return $database
                ->queryInstruction($query)
                ->rowCount();
        });
    }

    /**
     * @return int
     */
    public function updateOne()
    {
        $query = clone $this;
        $query->limit(1);

        return $this->update($query);
    }

}