<?php

namespace FpDbTest\QueryBuilder;

class QueryBuilderParametersCountException extends \Exception {}

class QueryBuilder {
    private Template $template;

    public function compile(string $template, array $params = [])
    {
        $parsedTemplate = new Template($template);
        $tokens = $parsedTemplate->getValueTokens();

        if (count($tokens) !== count($params)) {
            throw new QueryBuilderParametersCountException('Value tokens count and params count must be equal');
        }

        foreach ($tokens as $index => $token) {
            $token->setValue(new QueryParam($params[$index]));
        }

        return $parsedTemplate->getRepresantation();
    }
}
