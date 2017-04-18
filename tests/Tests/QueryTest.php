<?php

namespace Tests;

use Deimos\Builder\Builder;
use Deimos\Config\Config;
use Deimos\Database\Database;
use Deimos\Helper\Helper;

class QueryTest extends \PHPUnit\Framework\TestCase
{

    private $table = 'test';

    /**
     * @var Database
     */
    protected $db;

    public function setUp()
    {
        parent::setUp();

        $builder = new Builder();
        $helper  = new Helper($builder);

        $config = new Config(
            $helper,
            dirname(__DIR__)
        );

        $this->db = new Database($config->get('config'));
    }

    protected function createDb()
    {

        $this->db->rawQuery('CREATE TABLE IF NOT EXISTS ' . $this->table . ' (
      id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
      name varchar(32) NOT NULL,
      value varchar(255) NOT NULL
    );
');

    }

    public function testInsert()
    {
        $this->createDb();

        $name = __LINE__;
        $value = __LINE__;

        $this->db->insert() // 1
            ->from($this->table)
            ->value('name', $name)
            ->value('value', $value)
            ->insert();

        $pid = getmypid() OR $pid = getmyinode();

        $this->db->insert() // 2
            ->from($this->table)
            ->value('name', $pid)
            ->value('value', $pid)
            ->insert();
        $this->db->insert() // 3
            ->from($this->table)
            ->value('name', $pid)
            ->value('value', $pid)
            ->insert();
        $this->db->insert() // 4
            ->from($this->table)
            ->value('name', $pid)
            ->value('value', $pid)
            ->insert();

        $Q = $this->db->query()
            ->from($this->table)
            ->where('id', '<', 2)
            ->whereOr('name', $name);

        $this->assertTrue($Q->count() > 0);

        $str = str_replace('"', '', (string)$Q);

        $this->assertRegExp(
            '~SELECT\s+\*\s+FROM\s+' . $this->table . '~',
            $str
        );

        $lastId = $this->db->query()
            ->from($this->table)
            ->orderBy('id', 'DESC')
            ->select('id')
            ->findOne();

        $this->assertTrue(isset($lastId['id']));

        $this->assertTrue(
            $this->db->delete()
                ->from($this->table)
                ->where('id', $lastId['id'])
                ->delete() > 0
        );

        $this->db->update()
            ->from($this->table)
            ->where('id', 3)
            ->set('value', $this->db->raw('value*3'))
            ->update();

        $allResults = $this->db->query()
            ->from($this->table)->find();

        $this->assertEquals(
            count($allResults),
            3
        );

        $Q = $this->db->query()
            ->from($this->table)
            ->where('id', 3)
            ->findOne();

        $this->assertEquals(
            $Q['name'] * 3,
            $Q['value']
        );

        $this->assertTrue(
            $this->db->query()
                ->from($this->table)
                ->where('id', $lastId['id'])
                ->count() == 0
        );
    }

}
