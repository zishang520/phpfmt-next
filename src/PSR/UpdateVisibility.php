<?php
class UpdateVisibility extends FormatterPass
{
    public function candidate($source, $foundTokens): bool
    {
        if (isset($foundTokens[T_PRIVATE]) || isset($foundTokens[T_PROTECTED]) || isset($foundTokens[T_PUBLIC])) {
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

            if (T_PUBLIC == $id) {
                $this->walkUntil(T_STRING);
                list(, $text) = $this->inspectToken(0);
                $this->appendCode('function ' . ucfirst($text));
                continue;
            } elseif (T_PROTECTED == $id || T_PRIVATE == $id) {
                $this->walkUntil(T_STRING);
                list(, $text) = $this->inspectToken(0);
                $this->appendCode('function ' . lcfirst($text));
                continue;
            }

            $this->appendCode($text);
        }

        return $this->code;
    }
}
