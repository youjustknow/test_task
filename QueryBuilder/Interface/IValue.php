<?php

namespace FpDbTest\QueryBuilder\Interface;

interface IValue {
    public function getDefault();

    public function getRawString();

    public function getIdentifier();

    public function getString();

    public function getNumber();

    public function getFloat();

    public function getArray();
}