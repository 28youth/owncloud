<?php 

namespace XigeCloud\Services;

use Illuminate\Support\Facades\Redis;

class RedisLock
{

    const LOCK_SUCCESS = 'OK';
    const IF_NOT_EXIST = 'NX';
    const MILLISECONDS_EXPIRE_TIME = 'PX';

    const RELEASE_SUCCESS = 1;

    /**
     * 尝试获取锁
     * 
     * @param String $key               锁
     * @param String $requestId         请求id
     * @param int $expireTime           过期时间
     * @return bool                     是否获取成功
     */
    public static function tryGetLock(String $key, String $requestId, int $expireTime)
    {
        $result = Redis::set($key, $requestId, self::MILLISECONDS_EXPIRE_TIME, $expireTime, self::IF_NOT_EXIST);

        return self::LOCK_SUCCESS === (string)$result;
    }

    /**
     * 解锁
     * 
     * @param  String $key          锁
     * @param  String $requestId    请求ID
     * @return bool                 是否成功
     */
    public static function releaseLock(String $key, String $requestId)
    {
        $lua = "if redis.call('get', KEYS[1]) == ARGV[1] then return redis.call('del', KEYS[1]) else return 0 end";

        $result = Redis::eval($lua, 1, $key, $requestId);
        
        return self::RELEASE_SUCCESS === $result;
    }
}