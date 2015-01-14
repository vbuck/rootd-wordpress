<?php

/**
 * Rootd template filter class.
 *
 * PHP Version 5
 *
 * @package   Rootd
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

class Rootd_Template_Filter
{

    /**
     * Process a directive on the given input.
     * 
     * @param string $input      The input string.
     * @param string $directive  The directive.
     * @param array  $parameters The filter parameters.
     * 
     * @return string
     */
    protected function _applyFilter($input = '', $directive, $parameters)
    {
        // Support object properties or methods
        $parts = explode('.', preg_replace('/[{}]*/', '', $directive));

        foreach ($parameters as $key => $parameter) {
            $inputDirective = '{{' . $key . '}}';

            // Simple scalar value filter
            if (is_scalar($parameter) && $inputDirective == $directive) {
                $input = str_replace($directive, $parameter, $input);
            } else if ($parameter instanceof Rootd_Object && $key == $parts[0]) { // Filter for Rootd_Object data
                $input = str_replace($directive, $parameter->getDataUsingMethod($parts[1]), $input);
            } else if(is_object($parameter) && $key == $parts[0]) { // Filter for other object methods
                if (method_exists(array($parameter, $parts[1]))) {
                    $input = str_replace($directive, call_user_func(array($parameter, $parts[1])), $input);
                }
            }
        }

        return $input;
    }

    /**
     * Filter input for directives.
     * 
     * @param string $input      The input string.
     * @param array  $parameters The filter parameters.
     * 
     * @return string
     */
    public function filter($input = '', $parameters = array())
    {
        foreach ($this->getDirectives($input) as $directive) {
            $input = $this->_applyFilter($input, $directive, $parameters);
            
            // Clear unset directives
            $input = str_replace($directive, '', $input);
        }

        return $input;
    }

    /**
     * Get all directives in the input string.
     * 
     * @param string $input The input string.
     * 
     * @return array
     */
    public function getDirectives($input = '')
    {
        preg_match_all('/\{\{([^}]+)\}\}/', $input, $matches);

        if (isset($matches[0]) && is_array($matches[0])) {
            return $matches[0];
        }

        return array();
    }

}