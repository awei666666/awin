<?php 

namespace Awin\Xianliu;

use Redis;

class Bulong
{

    private $dataAmount;

    private $bitArrayLen;

    private $hashFunctionAmount;

    private $hashValuePool = [];

    //这里使用int模拟一个bit, int的0对应bit 0, int的1对应bit 1.
    //为了支持删除操作,1bit 需要扩展为多个bit,从而能够计数
    private $bitArray = [];

    public function __construct($dataAmount, $bitArrayLen)
    {
        $this->dataAmount = $dataAmount;
        $this->bitArrayLen = $bitArrayLen;
        $this->calculateOptimumHashFunctionAmount();
        $this->initBitArray();
    }

    public function add($str)
    {
        $this->mockHashFunction($str);
        foreach ($this->hashValuePool as $value) {
            $this->bitArray[$value] = $this->bitArray[$value] + 1;
        }
    }

    public function find($str)
    {
        $existsFlag = true;

        $this->mockHashFunction($str);
        foreach ($this->hashValuePool as $value) {
            if ($this->bitArray[$value] <= 0) {
                $existsFlag = false;
            }
        }
        return $existsFlag;
    }

    public function delete($str)
    {
        $response = true;
        if ($this->find($str)) {
            foreach ($this->hashValuePool as $value) {
                $this->bitArray[$value] = $this->bitArray[$value] - 1;
            }
        } else {
            $response = false;
        }

        return $response;
    }

    private function calculateOptimumHashFunctionAmount()
    {
        $this->hashFunctionAmount = ceil(($this->bitArrayLen / $this->dataAmount) * log(2));
    }

    private function initBitArray()
    {
        $this->bitArray = array_fill(0, $this->bitArrayLen, 0);
    }


    private function getRandSeed($str)
    {
        return crc32($str);
    }

    private function mockHashFunction($str)
    {
        $this->hashValuePool = [];
        $seed = $this->getRandSeed($str);
        mt_srand($seed);
        for ($i = 0; $i < $this->hashFunctionAmount; $i++) {
            $this->hashValuePool[] = mt_rand(0, $this->bitArrayLen - 1);
        }
    }

    public function save($callback)
    {
        $callback($this->bitArray);
    }

    /**
     * 导入
     *
     * @param function $callback
     * @return void
     */
    public function obtain($callback)
    {
        $this->bitArray = $callback();
    }
    

}