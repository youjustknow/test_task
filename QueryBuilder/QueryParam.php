<?php

namespace FpDbTest\QueryBuilder;

class QueryParamTypeException extends \Exception {}

class QueryParam {
    private mixed $value;

    public function __construct(mixed $param)
    {
        if (
            !in_array(gettype($param), ['string', 'array', 'integer', 'double', 'boolean', 'NULL', 'object']) ||
            gettype($param) === 'object' && !($param instanceof SkipToken)
        ) {
            throw new QueryParamTypeException(
                "QueryParam type must be one of 'string', 'array', 'integer', 'double', 'bool', 'NULL', 'object'. Got - ".
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
            case 'object': if ($this->value instanceof SkipToken) return $this->value;
        }
    }

    public function getRawString()
    {
        if ($this->value instanceof SkipToken) return $this->value;

        return (string) $this->value;
    }

    public function getIdentifier()
    {
        if ($this->value instanceof SkipToken) return $this->value;

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
        if ($this->value instanceof SkipToken) return $this->value;

        return sprintf("'%s'", $this->value);
    }

    public function getNumber()
    {
        if ($this->value instanceof SkipToken) return $this->value;

        if (gettype($this->value) === 'NULL') return 'NULL';

        return intval($this->value);
    }

    public function getFloat()
    {
        if ($this->value instanceof SkipToken) return $this->value;

        if (gettype($this->value) === 'NULL') return 'NULL';

        try {
            return floatval($this->value);
        } catch (Exception $e) {
            throw new Exception('Fuck');
        }
    }

    public function getArray()
    {
        if ($this->value instanceof SkipToken) return $this->value;

        $values = [];

        foreach ($this->value as $key => $value) {
            if (gettype($key) === 'integer') {
                $values[] = $value;
            } else {
                $param = new QueryParam($value);
                $values[] = sprintf("`%s` = %s", $key, $param->getDefault());
            }
        }

        return implode(', ', $values);
    }
}
