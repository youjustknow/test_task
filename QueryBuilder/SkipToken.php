<?php

namespace FpDbTest\QueryBuilder;

class SkipToken implements \Stringable, Interface\IValue {
    public function getDefault()
    {
        return $this;
    }

    public function getRawString()
    {
        return $this;
    }

    public function getIdentifier()
    {
        return $this;
    }

    public function getString()
    {
        return $this;
    }

    public function getNumber()
    {
        return $this;
    }

    public function getFloat()
    {
        return $this;
    }

    public function getArray()
    {
        return $this;
    }

    public function __toString()
    {
        return "\0";
    }
}
