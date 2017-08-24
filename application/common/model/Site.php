<?php
namespace app\common\model;


class Site extends Base
{
    // 设置当前模型对应的完整数据表名称
    // protected $table = 'mihua_site';
    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
    }
    public function period()
    {
        return $this->hasMany('period','sid')->field('id,sid,title,amount,rate,desc,status');
    }
    public function bankroll()
    {
        return $this->hasMany('bankroll','sid')->field('id,sid,title,amount,rate,desc,status');
    }
    public function getSiteInfo(array $params) {
        $map = [
            'key' => $params['key']
        ];
        $findRow = $this->where($map)->find();

        if ($findRow->period == $findRow->bankroll)
        {
            $findRow->period()->saveAll([
                ['title' => '借款5天', 'amount' => '5', 'rate' => 8, 'desc' => '借款5天服务费将收取总金额的8%', 'status' => 1],
                ['title' => '借款10天', 'amount' => '10', 'rate' => 10, 'desc' => '借款10天服务费将收取总金额的10%', 'status' => 1]
            ]);
            $findRow->bankroll()->saveAll([
                ['title' => '额度500', 'amount' => '50000', 'rate' => 0, 'desc' => '借款金额未500', 'status' => 1],
                ['title' => '额度1000', 'amount' => '100000', 'rate' => 0, 'desc' => '借款金额未1000', 'status' => 1],
                ['title' => '额度1500', 'amount' => '150000', 'rate' => 0, 'desc' => '借款金额未1500', 'status' => 1]
            ]);
        }
        return $findRow;
    }
}