<?php

namespace FpDbTest\QueryBuilder;

class SkipToken {
    public function __toString()
    {
        return "\0";
    }
}
