<?php

namespace Lemanwang\PhpTools\Task;

class TaskCommon
{
    /**
     *  获取当前进程的pid
     * @return false|int
     */
    public function getPid() {
        if(!function_exists(posix_getpid()))
            return getmypid();
        return posix_getpid();
    }


    /**
     *  执行命令行
     * @param $cli_cmd
     * @return void
     */
    public function execCmd($cli_cmd){
        pclose(popen($cli_cmd,'r')); //启动进程异步执行
    }


    public function kill222($pid,&$output,&$return){
        exec("kill -9 {$pid}", $output, $return); //posix_kill($pid,9);
    }

}