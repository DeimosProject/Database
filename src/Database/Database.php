<?php

namespace Deimos\Database;

use Deimos\Helper\Exceptions\ExceptionEmpty;
use Deimos\QueryBuilder\AbstractAdapter;
use Deimos\QueryBuilder\Adapter;
use Deimos\QueryBuilder\Instruction;
use Deimos\QueryBuilder\QueryBuilder;
use Deimos\QueryBuilder\RawQuery;
use Deimos\Slice\Slice;

class Database
{

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @var Slice
     */
    protected $slice;

    /**
     * @var array
     */
    protected $adapters = [
        'mysql'  => Adapter\MySQL::class,
        'sqlite' => Adapter\SQLite::class,
        'pgsql'  => Adapter\PostgreSQL::class,
    ];

    /**
     * @var AbstractAdapter[]
     */
    protected $adapterProviders = [];

    /**
     * @var QueryBuilder[]
     */
    protected $queryBuilders;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var \PDOStatement[]
     */
    protected $statements = [];

    /**
     * @var string
     */
    protected $defaultConnection;

    /**
     * Database constructor.
     *
     * @param $slice
     * @param $defaultConnection
     *
     * @throws ExceptionEmpty
     */
    public function __construct(Slice $slice, $defaultConnection = 'default')
    {
        $this->slice             = $slice;
        $this->defaultConnection = $defaultConnection;

        if (isset($this->slice['adapter']))
        {
            $this->slice = $this->slice->make([
                $defaultConnection => $this->slice->asArray()
            ]);
        }

//        $this->connect($defaultConnection);
//        $this->queryBuilder[$defaultConnection] = new QueryBuilder($this->adapter);
    }

    /**
     * @param string $connection
     *
     * @return QueryBuilder
     */
    public function queryBuilder($connection = null)
    {
        $key = $connection ?? $this->defaultConnection;

        if (!isset($this->queryBuilders[$key]))
        {
            $this->connection($key); // init connection
            $this->queryBuilders[$key] = new QueryBuilder($this->adapterProviders[$key]);
        }

        return $this->queryBuilders[$key];
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
     * @param string $connection
     *
     * @return \PDOStatement
     */
    public function rawQuery($sql, array $attributes = [], $connection = null)
    {
        if (empty($this->statements[$sql]))
        {
            $this->statements[$sql] = $this->connection($connection)->prepare($sql);
        }

        $this->statements[$sql]->execute($attributes);

        return $this->statements[$sql];
    }

    /**
     * @param string $sql
     * @param string $connection
     *
     * @return int
     */
    public function exec($sql, $connection = null)
    {
        return $this->connection($connection)->exec($sql);
    }

    /**
     * @return Queries\Query
     */
    public function query($connection = null)
    {
        return new Queries\Query($this, $connection ?: $this->defaultConnection);
    }

    /**
     * @return Queries\Insert
     */
    public function insert($connection = null)
    {
        return new Queries\Insert($this, $connection ?: $this->defaultConnection);
    }

    /**
     * @return Queries\Update
     */
    public function update($connection = null)
    {
        return new Queries\Update($this, $connection ?: $this->defaultConnection);
    }

    /**
     * @return Queries\Delete
     */
    public function delete($connection = null)
    {
        return new Queries\Delete($this, $connection ?: $this->defaultConnection);
    }

    /**
     * @param Instruction $object
     *
     * @return \PDOStatement
     */
    public function queryInstruction(Instruction $object, $connection = null)
    {
        return $this->rawQuery((string)$object, $object->attributes(), $connection);
    }

    /**
     * @param string $connection
     *
     * @return Connection
     */
    public function connection($connection = null)
    {
        $type = $connection ?? $this->defaultConnection;

        if (!isset($this->connection[$type]))
        {
            $slice = $this->slice->getSlice($type);

            $key     = $slice->getRequired('adapter');
            $adapter = $this->adapters[$key];

            $this->adapterProviders[$type] = new $adapter();

            $this->adapterProviders[$type]->setHost($slice->getData('host'));
            $this->adapterProviders[$type]->setPort($slice->getData('port'));

            if (method_exists($this->adapterProviders[$type], 'setPath'))
            {
                $this->adapterProviders[$type]->setPath($slice->getData('path'));
            }

            $this->adapterProviders[$type]->setDbName($slice->getData('database'));

            $this->adapterProviders[$type]->setConnection($this);

            $this->adapterProviders[$type]->setPDOClass(Connection::class);

            $options = [
                Connection::ATTR_DEFAULT_FETCH_MODE => Connection::FETCH_ASSOC,
                //Connection::ATTR_EMULATE_PREPARES   => true,
                //Connection::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                //Connection::ATTR_PERSISTENT         => false,
                //Connection::MYSQL_ATTR_INIT_COMMAND       => 'SET NAMES utf8mb4'
            ];

            if ($this->adapterProviders[$type]->name() !== 'sqlite')
            {
                $options[Connection::ATTR_ERRMODE] = Connection::ERRMODE_EXCEPTION;
            }

            $this->connection[$type] = $this->adapterProviders[$type]->connection(
                $slice->getData('username'),
                $slice->getData('password'),
                $slice->getData('options', $options)
            );
        }

        return $this->connection[$type];
    }

}
