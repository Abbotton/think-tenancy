<?php

declare(strict_types=1);

/*
 * ThinkPHP多租户扩展.
 *
 * @author Abbotton <uctoo@foxmail.com>
 */

namespace think\tenancy\commands;

use Symfony\Component\Finder\Finder;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class Publish extends Command
{
    protected function configure(): void
    {
        $this->setName('tenants:publish')->setDescription('发布扩展资源文件');
    }

    /**
     * Run database seeders.
     */
    protected function execute(Input $input, Output $output): void
    {
        $assetsDir = __DIR__.'/../database';
        $this->copyDir($assetsDir, app()->getRootPath().'database', true);
    }

    /**
     * 递归复制目录文件.
     *
     * @param  string  $dir  源目录
     * @param  string  $src  目标目录
     * @param  bool  $cover  是否覆盖文件
     */
    protected function copyDir($dir, $src, $cover = false): void
    {
        if (is_dir($src) && ! $cover) {
            if ($this->output->confirm($this->input, "确认覆盖资源文件目录[{$src}]? [y]/n")) {
                $cover = true;
            }
        } else {
            if (! is_dir($src)) {
                mkdir($src, 0755);
            }
            $cover = true;
        }
        if ($cover) {
            $finder = new Finder();
            foreach ($finder->in($dir) as $file) {
                $path = $file->getRealPath();
                $makePath = $src.\DIRECTORY_SEPARATOR.$file->getRelativePath().\DIRECTORY_SEPARATOR.$file->getFilename();
                if (is_dir($path)) {
                    if (! is_dir($makePath)) {
                        mkdir($makePath, 0755);
                    }
                } else {
                    copy($path, $makePath);
                }
            }
        }
        if ($cover) {
            $this->output->writeln("<info>[{$dir}] to [{$src}] 资源文件写入成功 successfully!</info>");
        }
    }
}
