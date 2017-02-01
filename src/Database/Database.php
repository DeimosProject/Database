<?php

namespace Deimos\Database;

use Deimos\Config\ConfigObject;
use Deimos\Helper\Exceptions\ExceptionEmpty;
use Deimos\QueryBuilder\Adapter;
use Deimos\QueryBuilder\Instruction;
use Deimos\QueryBuilder\QueryBuilder;
use Deimos\QueryBuilder\RawQuery;

class Database
{

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @var ConfigObject
     */
    protected $config;

    /**
     * @var array
     */
    protected $adapters = [
        'mysql'  => Adapter\MySQL::class,
        'sqlite' => Adapter\SQLite::class,
    ];

    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * Database constructor.
     *
     * @param $config
     *
     * @throws ExceptionEmpty
     */
    public function __construct(ConfigObject $config)
    {
        $this->config = $config;

        $this->connect();

        $this->queryBuilder = new QueryBuilder($this->adapter);
    }

    /**
     * @return Transaction
     */
    public function transaction()
    {
        if (!$this->transaction)
        {
            $this->transaction = new Transaction($this);
        }

        return $this->transaction;
    }

    /**
     * @param string $sql
     * @param array  $attributes
     *
     * @return RawQuery
     */
    public function raw($sql, array $attributes = [])
    {
        return new RawQuery($sql, $attributes);
    }

    /**
     * @param string $sql
     * @param array  $attributes
     *
     * @return \PDOStatement
     */
    public function rawQuery($sql, array $attributes = [])
    {
        $statement = $this->connection()->prepare($sql);
        $statement->execute($attributes);

        return $statement;
    }

    /**
     * @param string $sql
     *
     * @return int
     */
    public function exec($sql)
    {
        return $this->connection()->exec($sql);
    }

    /**
     * @return Queries\Query
     */
    public function query()
    {
        return new Queries\Query($this->queryBuilder, $this);
    }

    /**
     * @return Queries\Insert
     */
    public function insert()
    {
        return new Queries\Insert($this->queryBuilder, $this);
    }

    /**
     * @return Queries\Update
     */
    public function update()
    {
        return new Queries\Update($this->queryBuilder, $this);
    }

    /**
     * @return Queries\Delete
     */
    public function delete()
    {
        return new Queries\Delete($this->queryBuilder, $this);
    }

    /**
     * @param Instruction $object
     *
     * @return \PDOStatement
     */
    public function queryInstruction(Instruction $object)
    {
        return $this->rawQuery((string)$object, $object->attributes());
    }

    /**
     * @return Connection
     */
    public function connection()
    {
        return $this->connection;
    }

    /**
     * @return Connection
     *
     * @throws ExceptionEmpty
     */
    protected function connect()
    {
        if (!$this->connection)
        {
            $key     = $this->config->getRequired('adapter');
            $adapter = $this->adapters[$key];

            $this->adapter = new $adapter();

            $this->adapter->setHost($this->config->get('host'));
            $this->adapter->setPort($this->config->get('port'));

            if (method_exists($this->adapter, 'setPath'))
            {
                $this->adapter->setPath($this->config->get('path'));
            }

            $this->adapter->setDbName($this->config->get('database'));

            $this->adapter->setConnection($this);

            $this->adapter->setPDOClass(Connection::class);

            $this->connection = $this->adapter->connection(
                $this->config->get('username'),
                $this->config->get('password')
            );
        }

        return $this->connection;
    }

}
