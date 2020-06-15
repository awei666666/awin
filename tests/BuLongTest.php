<?php 

namespace Awin\Test;

use Awin\Xianliu\Bulong;

class BuLongTest  
{

    public function index()
    {
        $testData = [
            'data' => ['123', 'abc', 'aaa', 'ssssssssssss', '中国'],
            'exists' => ['aaa', '中国', 'sss', '123', '任命'],
            'delete' => ['aaa', '中国']
        ];
        
        echo '测试一 :'.PHP_EOL;
        $this->run($testData, 1000, 2000);
        
        
        echo '测试二 :'.PHP_EOL;
        // //这个出现的错误率,我们在删除aaa后,通过find查找,发现仍然会存在
        $this->run($testData, 1, 10000);
    }

  
    public function run($testData = [], $dataAmount, $bitArrayLen)
    {
        $bloomFilter = new Bulong($dataAmount,$bitArrayLen );
        foreach ($testData['data'] as $value) {
            $bloomFilter->add($value);
        }

        $bloomFilter->save(function($data){
            print_r($data);

        });
        exit;

        echo PHP_EOL. 'Original Data:'.PHP_EOL;
        foreach ($testData['data'] as $value) {
            echo $value . ';    ';
        }

        echo PHP_EOL. 'Before Delete:'.PHP_EOL;
        foreach ($testData['exists'] as $value) {
            $res = $bloomFilter->find($value) ? 'true' : 'false';
            echo $value . ' : ' . $res . ';    ';
        }

        echo  PHP_EOL.'Delete:'.PHP_EOL;
        foreach ($testData['delete'] as $value) {
            $res = $bloomFilter->delete($value) ? 'true' : 'false';
            echo $value . ' : ' . $res . ';    ';
        }

        echo PHP_EOL. 'After Delete:'.PHP_EOL;
        foreach ($testData['exists'] as $value) {
            $res = $bloomFilter->find($value) ? 'true' : 'false';
            echo $value . ' : ' . $res . ';    ';
        }

    }



    public function index2()
    {
        $items = array("444","5555","6666"); //定义一个集合items
        //定义一个BloomFilter对象并将集合元素添加进过滤器中.
        $filter=new Bulong(100,3);
        $filter->add($items);
        // $filter->add('韩威兵');
        //判断items1中的元素是否在items集合中
        $items1 =array("韩威兵","5555","222");
        foreach ($items1 as $item) {
            var_dump(($filter->find($item)));
        }
    }
}
