<?php
class ExtractMethods extends FormatterPass
{
    private $functionStack = [];

    public function candidate($source, $foundTokens): bool
    {
        if (isset($foundTokens[T_FUNCTION])) {
            return true;
        }

        return false;
    }

    public function format($source): string
    {
        $this->tkns = token_get_all($source, TOKEN_PARSE);
        $this->code = '';
        while (list($index, $token) = eachArray($this->tkns)) {
            list($id, $text) = $this->getToken($token);
            $this->ptr = $index;

            if (T_CLASS == $id) {
                $this->appendCode($text);
                $this->walkUntil(T_STRING);

                list(, $className) = $this->inspectToken(0);
                $this->appendCode(' ' . $className);

                $this->walkUntil(ST_CURLY_OPEN);
                $this->appendCode(' ' . ST_CURLY_OPEN);

                $startPtr = $this->ptr;
                $endPtr = $this->ptr;
                $this->refWalkCurlyBlock($this->tkns, $endPtr);

                // $this->extractMethodsFrom($className, $startPtr, $endPtr);
                continue;
            }

            $this->appendCode($text);
        }

        return $this->code;
    }

    private function extractMethodsFrom($className, $startPtr, $endPtr)
    {
        echo $className, ' ', $startPtr, ' <-> ', $endPtr;
    }
}
