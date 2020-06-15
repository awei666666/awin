<?php 
namespace Awin\Xianliu;

use Redis;

class Bulong
{
  
    private $ArrayLen;
    private $bitArray = [];

    public function __construct($ArrayLen = 1000)
    {
        // $this->dataAmount = $dataAmount;
        $this->ArrayLen = $ArrayLen;

    }

    public function add($str)
    {
        $bit = $this->getBit($str);

        if(isset($this->bitArray[$bit]) && !empty($this->bitArray[$bit])){
            $this->bitArray[$bit] = $this->bitArray[$bit]+1;
        }else{
            $this->bitArray[$bit] = 1;
        }
        // $this->bitArray[$bit];
    }

    public function find($str)
    {
        $bit = $this->getbit($str);
        if(!isset($this->bitArray[$bit])){
            return false;
        }

        if(empty($this->bitArray[$bit])  ||  ($this->bitArray[$bit] < 0)){
            return false;
        }

        // print_r($this->bitArray);
        return true;
    }

    public function delete($str)
    {
        
        $bit = $this->getbit($str);
        if(!isset($this->bitArray[$bit])){
            return false;
        }

        if(empty($this->bitArray[$bit])  ||  ($this->bitArray[$bit] < 0)){
            return false;
        }
        $this->bitArray[$bit]--;
        return true;
    }

    public function getBit($str)
    {
        $crc32 = crc32($str);
        $bit = $crc32 % $this->ArrayLen;
        return $bit;
    }

    public function save($callback)
    {
        $callback($this->bitArray);
    }

    public function obtain($callback)
    {
        $this->bitArray = $callback();
    }



   


   
}