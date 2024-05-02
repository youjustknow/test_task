<?php

namespace FpDbTest\QueryBuilder;

class QueryParamTypeException extends \Exception {}

class QueryParam implements Interface\IValue {
    private mixed $value;

    public function __construct(mixed $param)
    {
        if (
            !in_array(gettype($param), ['string', 'array', 'integer', 'double', 'boolean', 'NULL'])
        ) {
            throw new QueryParamTypeException(
                "QueryParam type must be one of 'string', 'array', 'integer', 'double', 'bool', 'NULL'. Got - ".
                gettype($param)
            );
        }

        $this->value = $param;
    }

    public function getDefault()
    {
        switch (gettype($this->value)) {
            case 'string': return $this->getString();
            case 'array': return $this->getArray();
            case 'integer': return $this->getNumber();
            case 'double': return $this->getFloat();
            case 'bool': return $this->getNumber();
            case 'NULL': return 'NULL';
        }
    }

    public function getRawString()
    {
        if (!is_string($this->value)) {
            throw new QueryParamTypeException("Expected string. Got ".$this->value);
        }

        return (string) $this->value;
    }

    public function getIdentifier()
    {
        if (!is_array($this->value) && !is_string($this->value)) {
            throw new QueryParamTypeException("Expected array or string. Got ".$this->value);
        }

        if (!is_array($this->value)) {
            return sprintf("`%s`", $this->value);
        } else {
            $identifiers = [];

            foreach ($this->value as $identifier) {
                $identifiers[] = sprintf("`%s`", $identifier);
            }

            return implode(', ', $identifiers);
        }
    }

    public function getString()
    {
        if (!is_string($this->value)) {
            throw new QueryParamTypeException("Expected string. Got ".$this->value);
        }

        return sprintf("'%s'", $this->value);
    }

    public function getNumber()
    {
        if (gettype($this->value) === 'NULL') return 'NULL';
        if (!is_numeric(trim($this->value))) {
            throw new QueryParamTypeException("Expected integer. Got ".$this->value);
        }

        return intval($this->value);
    }

    public function getFloat()
    {
        if (gettype($this->value) === 'NULL') return 'NULL';
        if (!is_float($this->value)) {
            throw new QueryParamTypeException("Expected float. Got ".$this->value);
        }

        return floatval($this->value);
    }

    public function getArray()
    {
        if (!is_array($this->value)) {
            throw new QueryParamTypeException("Expected array. Got ".gettype($this->value));
        }

        $values = [];

        foreach ($this->value as $key => $value) {
            if (gettype($key) === 'integer') {
                $values[] = $value;
            } else {
                if (is_array($value)) {
                    throw new QueryParamTypeException("Expected array of string, integer or double. Got array value ".$value);
                }

                $param = new QueryParam($value);
                $values[] = sprintf("`%s` = %s", $key, $param->getDefault());
            }
        }

        return implode(', ', $values);
    }
}
