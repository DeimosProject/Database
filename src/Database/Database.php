<?php

namespace Deimos\Database;

use Deimos\Config\ConfigObject;
use Deimos\Helper\Exceptions\ExceptionEmpty;
use Deimos\QueryBuilder\AbstractAdapter;
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
        'pgsql'  => Adapter\PostgreSQL::class,
    ];

    /**
     * @var AbstractAdapter
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
     * @var \PDOStatement[]
     */
    protected $statements = [];

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
     * @return QueryBuilder
     */
    public function queryBuilder()
    {
        return $this->queryBuilder;
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
        if (empty($this->statements[$sql]))
        {
            $this->statements[$sql] = $this->connection()->prepare($sql);
        }

        $this->statements[$sql]->execute($attributes);

        return $this->statements[$sql];
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
        return new Queries\Query($this);
    }

    /**
     * @return Queries\Insert
     */
    public function insert()
    {
        return new Queries\Insert($this);
    }

    /**
     * @return Queries\Update
     */
    public function update()
    {
        return new Queries\Update($this);
    }

    /**
     * @return Queries\Delete
     */
    public function delete()
    {
        return new Queries\Delete($this);
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

            $options = [
                Connection::ATTR_DEFAULT_FETCH_MODE => Connection::FETCH_ASSOC,
                //Connection::ATTR_EMULATE_PREPARES   => true,
                //Connection::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                //Connection::ATTR_PERSISTENT         => false,
                //Connection::MYSQL_ATTR_INIT_COMMAND       => 'SET NAMES utf8mb4'
            ];

            if ($this->adapter->name() !== 'sqlite')
            {
                $options[Connection::ATTR_ERRMODE] = Connection::ERRMODE_EXCEPTION;
            }

            $this->connection = $this->adapter->connection(
                $this->config->get('username'),
                $this->config->get('password'),
                $this->config->get('options', $options)
            );
        }

        return $this->connection;
    }

}
