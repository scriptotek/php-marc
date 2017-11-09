<?php


namespace Scriptotek\Marc;


trait MagicAccess
{
    public function __get($key)
    {
        // Convert key from underscore_case to camelCase.
        $key_uc = preg_replace_callback(
            '/_([a-z])/',
            function($matches) {
                return strtoupper($matches[1]);
            },
            $key
        );

        $method = 'get' . ucfirst($key_uc);
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method]);
        }
    }

}