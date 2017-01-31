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

    public function update()
    {
        return $this
            ->database
            ->queryInstruction($this)
            ->rowCount();
    }

    public function updateOne()
    {
        $self = clone $this;
        $self->limit(1);

        return $self
            ->database
            ->queryInstruction($self)
            ->rowCount();
    }

}