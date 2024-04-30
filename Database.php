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
        $queryBuilder = new QueryBuilder();

        $escapedArgs = $this->escapeArgs($args);
        $result = $queryBuilder->compile($query, $escapedArgs);

        return $result;
    }

    public function skip()
    {
        return new SkipToken();
    }

    private function escapeArgs(array $args)
    {
        foreach ($args as &$arg) {
            if ($arg instanceof SkipToken) continue;

            switch (gettype($arg)) {
                case 'string': $arg = $this->mysqli->real_escape_string($arg); break;
                case 'array':
                    $escapedArr = [];

                    foreach ($arg as $key => &$val) {
                        if (gettype($val) === 'string') $val = $this->mysqli->real_escape_string($val);

                        if (gettype($key) === 'string') {
                            $key = $this->mysqli->real_escape_string($key);
                        }
                        
                        $escapedArr[$key] = $val;
                    }
            }
        }

        return $args;
    }
}
