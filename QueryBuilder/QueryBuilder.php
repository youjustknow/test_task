<?php

namespace FpDbTest\QueryBuilder;

class QueryBuilderParametersCountException extends \Exception {}

class QueryBuilder {
    private \mysqli $mysqli;

    public function __construct(\mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function compile(string $template, array $params = [])
    {
        $escapedParams = $this->escapeParams($params);

        $parsedTemplate = new Template($template);
        $parsedTemplate->setParams($escapedParams);

        return $parsedTemplate->compile();
    }

    private function escapeParams(array $params)
    {
        foreach ($params as &$param) {
            switch (gettype($param)) {
                case 'string': $param = $this->mysqli->real_escape_string($param); break;
                case 'array':
                    $escapedArr = [];

                    foreach ($param as $key => &$val) {
                        if (gettype($val) === 'string') $val = $this->mysqli->real_escape_string($val);

                        if (gettype($key) === 'string') {
                            $key = $this->mysqli->real_escape_string($key);
                        }
                        
                        $escapedArr[$key] = $val;
                    }
            }
        }

        return $params;
    }
}
