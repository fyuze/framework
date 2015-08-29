<?php
namespace Fyuze\Database;

class Query
{
    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var string
     */
    protected $table;

    /**
     * @param DB $database
     * @param $table
     */
    public function __construct(DB $database, $table)
    {
        $this->db = $database;
        $this->table = $table;
    }
}
