<?php

namespace FpDbTest\QueryBuilder;

class TemplateToken {
    private QueryParam $value;
    private TemplateTokenType $type;

    public function __construct(TemplateTokenType $type) {
        $this->type = $type;
    }

    public function setValue(QueryParam $value) {
        $this->value = $value;
    }

    public function getValue() {
        switch ($this->type) {
            case TemplateTokenType::Integer: return $this->value->getNumber();
            case TemplateTokenType::Float: return $this->value->getFloat();
            case TemplateTokenType::Default: return $this->value->getDefault();
            case TemplateTokenType::Array: return $this->value->getArray();
            case TemplateTokenType::String: return $this->value->getString();
            case TemplateTokenType::Identifier: return $this->value->getIdentifier();
            case TemplateTokenType::RawString: return $this->value->getRawString();
        }
    }
}
