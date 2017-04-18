<?php

namespace Deimos\Database;

trait Instruction
{
    public function query()
    {
        $query = $this;

        return $this->database->transaction()->call(function ($database) use ($query)
        {
            /**
             * @var Database $database
             */
            return $database
                ->queryInstruction($query, $this->connection)
                ->rowCount();
        }, $this->connection);
    }
}