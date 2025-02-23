<?php
namespace Fyuze\Debug\Collectors;

use Fyuze\Database\Db;

class Database implements Collector
{
    /**
     * @var Db
     */
    protected $database;

    protected $db;

    /**
     * Database constructor.
     * @param Db $database
     */
    public function __construct(Db $database)
    {
        $this->db = $database;
    }

    /**
     * @return string
     */
    public function tab()
    {
        return [
            'title' => count($this->db->getQueries()) . ' Queries'
        ];
    }
}
