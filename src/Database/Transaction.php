<?php

namespace Deimos\Database;

class Transaction
{

    const STATE_ROLLBACK = 0;
    const STATE_COMMIT   = 1;

    /**
     * @var Database
     */
    protected $database;

    /**
     * @var null|int
     */
    protected $state;

    /**
     * Transaction constructor.
     *
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @param callable $callback
     *
     * @return mixed
     */
    public function call(callable $callback, $connection = null)
    {
        $this->database->connection($connection)->beginTransaction();

        try
        {
            $result = $callback($this->database);

            $this->database->connection($connection)->commit();
            $this->state = static::STATE_COMMIT;

            return $result;
        }
        catch (\Exception $exception)
        {
            $this->database->connection($connection)->rollBack();
            $this->state = static::STATE_ROLLBACK;

            return null;
        }
    }

    /**
     * @return null|int
     */
    public function state()
    {
        return $this->state;
    }

}