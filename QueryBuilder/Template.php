<?php

namespace FpDbTest\QueryBuilder;

class Template {
    private array $tokens = [];
    private array $valueTokens = [];
    private $TYPES_MAP = [
        'd' => TemplateTokenType::Integer,
        'f' => TemplateTokenType::Float,
        '#' => TemplateTokenType::Identifier,
        'a' => TemplateTokenType::Array
    ];

    public function __construct(string $template)
    {
        $this->parseTemplate($template);
    }

    public function setParams($params)
    {
        $tokens = $this->valueTokens;
        $result = [];

        if (count($tokens) !== count($params)) {
            throw new QueryBuilderParametersCountException('Value tokens count and params count must be equal');
        }

        foreach ($tokens as $index => $token) {
            if ($params[$index] instanceof SkipToken) $token->setValue($params[$index]);
            else $token->setValue(new QueryParam($params[$index]));
        }
    }

    public function compile()
    {
        $tokens = $this->tokens;
        $result = [];

        foreach ($tokens as $token) {
            $result[] = $token->getValue();
        }

        $represantation = implode('', $result);

        $skipToken = new SkipToken();

        $represantationWithoutConditional = preg_replace("/{.*?".$skipToken.".*?}/", '', $represantation);
        $represantationWithoutConditionalBrackets = preg_replace('/[{}]/', '', $represantationWithoutConditional);

        return $represantationWithoutConditionalBrackets;
    }

    private function parseTemplate(string $template)
    {
        $currentIndex = 0;
        $tokens = [];
        $valueTokens = [];

        while (preg_match('/(.*?)(\?[daf#]?)/', $template, $matches, PREG_OFFSET_CAPTURE, $currentIndex)) {
            $currentIndex = $matches[2][1] + strlen($matches[2][0]);

            $strToken = $this->createToken(TemplateTokenType::RawString, $matches[1][0]);

            $valueTokenType = $this->getTokenType(strlen($matches[2][0]) > 1 ? $matches[2][0][1] : '');
            $valueToken = $this->createToken($valueTokenType);

            $tokens[] = $strToken;
            $tokens[] = $valueToken;
            $valueTokens[] = $valueToken;
        }

        if ($currentIndex < strlen($template)) {
            $strToken = $this->createToken(TemplateTokenType::RawString, substr($template, $currentIndex));
            $tokens[] = $strToken;
        }

        $this->tokens = $tokens;
        $this->valueTokens = $valueTokens;
    }

    private function getTokenType(string $typeSpecifier)
    {
        return $this->TYPES_MAP[$typeSpecifier] ?? TemplateTokenType::Default;
    }

    private function createToken(TemplateTokenType $type, mixed $value = null)
    {
        $token = new TemplateToken($type);
        if ($value !== null) $token->setValue(new QueryParam($value));

        return $token;
    }
}
