<?php 

if (!function_exists('getSize')) {
    /**
     * 获取 bit 流大小.
     * 
     * @param  integer  $bit
     * @param  boolean $array
     * 
     * @return float||array
     */
    function getSize($bit, $array = false){
        $type = ['Bytes', 'KB', 'MB', 'GB', 'TB'];  
        $box = ['1', '1024', '1048576', '1073741824', 'TB'];  
        for ($i = 0; $bit >= 1024; $i++) {
            $bit/=1024;  
        }
        if ($array) {
            return [(floor($bit * 100) / 100), $box[$i]];  
        }
        return (floor($bit * 100) / 100) . $type[$i];  
    }
}