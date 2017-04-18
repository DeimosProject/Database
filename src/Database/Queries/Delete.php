<?php

namespace Deimos\Database\Queries;

use Deimos\Database\Database;
use Deimos\QueryBuilder\Instruction;

/**
 * Class Delete
 *
 * @package Deimos\Database\Queries
 *
 * @method int delete()
 */
class Delete extends Instruction\Delete
{

    use \Deimos\Database\Instruction
    {
        query as delete;
    }

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

}