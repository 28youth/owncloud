<?php 

namespace XigeCloud\Files\Plugin;

use phpseclib\Net\SFTP;
use League\Flysystem\Util;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Sftp\SftpAdapter;
use League\Flysystem\Plugin\AbstractPlugin;

class MergeFile extends AbstractPlugin
{

    public function getMethod()
    {
        return 'append';
    }

    public function handle($path, $contents)
    {
    	$path = Util::normalizePath($path);

		$this->filesystem->assertPresent($path);

        $adapter = $this->filesystem->getAdapter();

        switch (true) {
        	case $adapter instanceof Local:

        		$location = $adapter->applyPathPrefix($path);
		        $size = file_put_contents($location, $contents, FILE_APPEND);

		        if ($size === false) return false;

		        return true;

        	break;
        	
        	case $adapter instanceof SftpAdapter:

		        $connection = $adapter->getConnection();
		        $adapter->ensureDirectory(Util::dirname($path));
		        
		        if (! $connection->put($path, $contents, SFTP::RESUME)) {
		            return false;
		        }

		        return true;

        	break;
        }
    }
}