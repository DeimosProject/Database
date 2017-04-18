<?php

namespace Deimos\Database\Queries;

use Deimos\Database\Connection;
use Deimos\Database\Database;
use Deimos\QueryBuilder\Instruction\Select;

class Query extends Select
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
    public function count()
    {
        $self = clone $this;
        $self->setSelect([
            'count' => $this->database->raw('COUNT(1)')
        ]);

        $data = $self->findOne();

        return $data['count'];
    }

    /**
     * @return array
     */
    public function find()
    {
        return $this
            ->database
            ->queryInstruction($this, $this->connection)
            ->fetchAll(Connection::FETCH_ASSOC);
    }

    /**
     * @return mixed
     */
    public function findOne()
    {
        $self = clone $this;
        $self->limit(1);

        $sth = $self
            ->database
            ->queryInstruction($self, $this->connection);

        $data = $sth->fetch(Connection::FETCH_ASSOC);
        $sth->closeCursor();

        return $data;
    }

}