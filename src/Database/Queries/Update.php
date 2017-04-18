<?php

namespace Deimos\Database\Queries;

use Deimos\Database\Database;
use Deimos\QueryBuilder\Instruction;

/**
 * Class Update
 *
 * @package Deimos\Database\Queries
 *
 * @method int update()
 */
class Update extends Instruction\Update
{

    use \Deimos\Database\Instruction {
        query as update;
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
