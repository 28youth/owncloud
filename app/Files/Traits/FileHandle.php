<?php 

namespace XigeCloud\Files\Traits;

use Cache;
use phpseclib\Net\SSH2;
use phpseclib\Crypt\RSA;

trait FileHandle
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
        $config = Cache::get('policies_mapwithname')[$this->connect];
        
        if (empty($config)) abort(500, '缺少上传配置');

        return $config;
    }

    protected function getPath($path)
    {
        $root = storage_path($path);
        if ($this->getConfig()['driver'] === 'sftp') {
            return  sprintf(
                '%s%s', 
                $this->filesystem()->getAdapter()->getRoot(),
                $path
            );
        }
        return $root;
    }

    protected function exec($bash): string
    {
        $driver = $this->getConfig()['driver'];
        if ($driver === 'sftp') {
            return $this->bash()->exec($bash);
        } elseif ($driver === 'local') {

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
    public function mergeFile($savepath, $savename, $block_list)
    {
        $ids = implode(" ", $block_list);

        $bash = <<<EOF
            if [ ! -d $savepath ]; then
                echo "error 文件路径不存在" && exit
            fi
            cd $savepath
            if [ -f $savename ];then
                echo "error 合并的文件已存在" && exit
            fi
            block=($ids)
            for chunk in \${block[@]}
            do
                if [ ! -f "\$chunk" ];then
                    echo "error \$chunk 文件块不存在" && exit
                fi
            done

            cat $ids > $savename
EOF;
        return $this->exec($bash);
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
                echo "error $file 文件不存在" && exit
            fi
            md5sum $file
EOF;
        $response = $this->exec($bash);
        preg_match('/(?<md5>[a-fA-F0-9]{32,32})/', $response, $match);

        return !empty($match['md5']) ? $match['md5'] : $response;
    }

    /**
     * 移动一个文件.
     * 
     * @param  string $origin_path 目标文件路径
     * @param  string $save_path   储存文件路径
     * 
     * @return error||empty
     */
    public function move($origin_path, $save_path)
    {
        if (empty($save_path) || empty($origin_path)) {
            abort(500, '移动文件路径错误～');
        }

        $bash = <<<EOF
            if [ -f $save_path ];then
                echo "error 移动的文件已存在" && exit
            fi
            if [ ! -f $origin_path ];then
                echo "error 待移动文件不存在" && exit
            fi

            mv $origin_path $save_path
EOF;
        return $this->exec($bash);
    }

    public function unzip($savepath, $filename)
    {
        $bash = <<<EOF
            if [ ! -d $savepath ]; then
                echo "error 文件路径不存在" && exit
            fi
            cd $savepath
            if [ ! -f $filename ];then
                echo "error 压缩包文件不存在" && exit
            fi
            filename=\$(basename "$filename")
            extension="\${filename##*.}"
            filename="\${filename%.*}"
            if [ \$extension = "zip" ];then
                mkdir \$filename && unzip -o $filename -d \$filename &> /dev/null
            elif [ \$extension = 'tar' ];then
                mkdir \$filename && tar -xvf $filename -C \$filename &> /dev/null
            elif [ \$extension = 'gz' ];then
                mkdir \$filename && tar -zxvf $filename -C \$filename &> /dev/null
            elif [ \$extension = 'bz2' ];then
                mkdir \$filename && tar -jxvf $filename -C \$filename &> /dev/null
            else
                echo "error 不支持的压缩包类型" && exit
            fi
            files=\$(ls \$filename)
            echo \$files
EOF;
            return $this->response($this->exec($bash));
    }

    protected function response($result)
    {
        preg_match('/(?<mark>error|errors|command)/', $result, $match);
        if (! empty($match['mark'])) {
            abort(500, $result);
        }
        return array_filter(preg_split('/[;\r\n]+/s', $result));
    }
}