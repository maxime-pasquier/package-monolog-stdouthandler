<?php

namespace Monolog\Formatter;

/**
 * Formats incoming records into a one-line colored string
 */
class ColorLineFormatter extends LineFormatter
{
    const
        COLOR_PATTERN = '~\[(?<closing>/?)(?<method>[a-z])(?:=(?<value>[a-z]+))?\]~';
    
    public static
        $colors = array(
            'black'  => '30',
            'red'    => '31',
            'green'  => '32',
            'yellow' => '33',
            'blue'   => '34',
            'purple' => '35',
            'cyan'   => '36',
            'white'  => '37',
        );
    
    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        $selfClassName = get_class();
        
        $callback = function($result) use($selfClassName)
        {
            $valueParameter = null;
            
            if( ! empty($result['closing']) )
            {
                $prefixMethodName = 'closing';
            }
            else
            {
                $prefixMethodName = 'opening';
                
                if( ! empty($result['value']) )
                {
                    $valueParameter = $result['value'];
                }
            }
            
            $methodName = sprintf(
                '%s%s',
                $prefixMethodName,
                ucfirst($result['method'])
            );
            
            if( ! method_exists($selfClassName, $methodName) )
            {
                return $input;
            }
            
            return $selfClassName::$methodName($valueParameter);
        };
        
        return preg_replace_callback(
            self::COLOR_PATTERN,
            $callback,
            parent::format($record)
        );
    }
    
    public static function openingC($color)
    {
        $color = strtolower($color);
        
        if( empty(self::$colors[$color]) )
        {
            return '';
        }
        
        return sprintf(
            "\033[%dm",
            self::$colors[$color]
        );
    }
    
    public static function closingC($color)
    {
        return "\033[0m";
    }
}
