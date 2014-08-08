<?php

namespace Monolog\Formatter;

/**
 * Formats incoming records into a one-line colored string
 */
class ColorLineFormatter extends LineFormatter
{
    const COLOR_PATTERN = '~\[(?<closingSlash>/?)c(?:=(?<valueParameter>[a-z]+))?\]~';
    
    private $colors = array(
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
        $output = parent::format($record);
        
        return preg_replace_callback(self::COLOR_PATTERN, array($this, 'applyMethods'), $output);
    }
    
    private function applyMethods(array $result)
    {
        // initialize value parameter
        $valueParameter = null;
        if( ! empty($result['valueParameter']) )
        {
            $valueParameter = $result['valueParameter'];
        }
        
        // case 'c' for color
        if( ! empty($result['closingSlash']) )
        {
            return $this->applyEndingColor();
        }
        
        return $this->applyBeginningColor($valueParameter);
    }
    
    private function applyBeginningColor($color)
    {
        $color = strtolower($color);
        
        if( empty($this->colors[$color]) )
        {
            return '';
        }
        
        return sprintf(
            "\033[%dm",
            $this->colors[$color]
        );
    }
    
    private function applyEndingColor()
    {
        return "\033[0m";
    }
}
