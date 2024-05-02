<?php

namespace FpDbTest;

use Exception;
use mysqli;

use FpDbTest\QueryBuilder\QueryBuilder;
use FpDbTest\QueryBuilder\SkipToken;

class Database implements DatabaseInterface
{
    private mysqli $mysqli;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function buildQuery(string $query, array $args = []): string
    {
        $queryBuilder = new QueryBuilder($this->mysqli);
        
        $result = $queryBuilder->compile($query, $args);

        return $result;
    }

    public function skip()
    {
        return new SkipToken();
    }
}
