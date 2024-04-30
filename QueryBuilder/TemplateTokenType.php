<?php

namespace FpDbTest\QueryBuilder;

enum TemplateTokenType {
    case Default;
    case String;
    case Array;
    case Integer;
    case Float;
    case Identifier;
    case RawString;
}
