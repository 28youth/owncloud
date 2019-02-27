<?php 

namespace XigeCloud\Files\Traits;

use Cache;
use phpseclib\Net\SSH2;
use phpseclib\Crypt\RSA;

trait BashHandle
{   
    /**
     * 获取服务器SSH2执行对象.
     * 
     * @return \phpseclib\Net\SSH2||error
     */
    public function bash()
    {
        $config = $this->getConfig();
        $ssh = new SSH2($config['host']);
        $key = new RSA();
        $key->loadKey($config['privateKey']);
        if (!$ssh->login($config['username'], $key)) {
            exit('Login Failed');
        }

        return $ssh;
    }

    protected function getConfig()
    {
        $config = Cache::get('policies_config_list')[$this->connect];//$this->category->policy;

        if (empty($config)) {

            abort(500, '缺少上传配置，请联系管理员～');
        }

        return $config;
    }

    protected function exec($bash)
    {
        $config = $this->getConfig();

        if ($config['driver'] === 'sftp') {

            return $this->bash()->exec($bash);

        } elseif ($config['driver'] === 'local') {

            return exec("$bash 2>&1");
        }
    }

    /**
     * 执行文件合并.
     * 
     * @param  string $savepath   文件块所在路径
     * @param  string $savename   合并文件名称
     * @param  array  $block_list 合并文件块数组
     * 
     * @return error||empty
     */
    public function execMerge($savepath, $savename, $block_list, callable $call)
    {
        $ids = implode(" ", $block_list);

        $bash = <<<EOF
            if [ ! -d $savepath ]; then
                echo "文件路径不存在"
                exit
            fi
            cd $savepath

            if [ -f $savename ];then
                echo "合并的文件已存在"
                exit
            fi

            block=($ids)
            for chunk in \${block[@]}
            do
                if [ ! -f "\$chunk" ];then
                    echo "\$chunk 文件块不存在"
                    exit
                fi
            done

            cat $ids > $savename
EOF;
        $response = $this->exec($bash);

        return call_user_func_array($call, [$response, $savename]);
    }

    /**
     * 计算文件md5.
     * 
     * @param  string $file
     * @return md5
     */
    public function calculmd5($file)
    {
        $bash = <<<EOF
            if [ ! -f $file ];then
                echo "$file 文件不存在"
                exit
            fi
            md5sum $file
EOF;
        $response = $this->exec($bash);
        preg_match('/(?<md5>[a-fA-F0-9]{32,32})/', $response, $match);

        return (strlen($match ? $match['md5'] : '') == 32) ? $match['md5'] : $response;
    }

    /**
     * 移动一个文件.
     * 
     * @param  string $origin_path 目标文件路径
     * @param  string $save_path   储存文件路径
     * 
     * @return error||empty
     */
    public function execMove($origin_path, $save_path)
    {
        if (empty($save_path) || empty($origin_path)) {

            abort(500, '移动文件路径错误～');
        }

        $bash = <<<EOF
            if [ -f $save_path ];then
                echo "移动的文件已存在"
                exit
            fi

            if [ ! -f $origin_path ];then
                echo "待移动文件不存在"
                exit
            fi

            mv $origin_path $save_path
EOF;
        return $this->exec($bash);
    }

    public function deleteChunks($value='')
    {
        # code...
    }
}