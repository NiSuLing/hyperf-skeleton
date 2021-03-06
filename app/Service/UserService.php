<?php

namespace App\Service;

use App\Job\UserJob;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;
use Hyperf\DbConnection\Db;

class UserService
{
    /**
     * @var DriverInterface
     */
    protected $driver;

    public function __construct(DriverFactory $driverFactory)
    {
        $this->driver = $driverFactory->get('default');
    }


    public function getUser(int $id)
    {
        $user =  Db::table('users')->where('id',$id)->get();
        return $user;
    }

    /**
     * 生产消息.
     * @param $params 数据
     * @param int $delay 延时时间 单位秒
     */
    public function push($params, int $delay = 0): bool
    {
        // 这里的 `ExampleJob` 会被序列化存到 Redis 中，所以内部变量最好只传入普通数据
        // 同理，如果内部使用了注解 @Value 会把对应对象一起序列化，导致消息体变大。
        // 所以这里也不推荐使用 `make` 方法来创建 `Job` 对象。
        $user =  Db::table('users')->where('id',$params['id'])->first();
        echo '<pre>';
        print_r($user);
        return $this->driver->push(new UserJob($user), $delay);
    }
}