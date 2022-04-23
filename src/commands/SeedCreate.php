<?php

declare(strict_types=1);

/*
 * ThinkPHP多租户扩展.
 *
 * @author Abbotton <uctoo@foxmail.com>
 */

namespace think\tenancy\commands;

use Phinx\Util\Util;
use think\console\Input;
use think\console\input\Argument as InputArgument;
use think\console\Output;
use think\migration\command\seed\Create as BaseCreate;

class SeedCreate extends BaseCreate
{
    private $path = '';

    protected function configure(): void
    {
        $this->setName('tenants:seed:create')
            ->setDescription('多租户创建新的数据填充文件')
            ->addArgument('name', InputArgument::REQUIRED, 'What is the name of the seeder?')
            ->addArgument('path', InputArgument::OPTIONAL, '指定数据填充文件存放目录')
            ->setHelp(sprintf('%sCreates a new database seeder%s', PHP_EOL, PHP_EOL));
    }

    /**
     * Create the new seeder.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function execute(Input $input, Output $output): void
    {
        $this->path = $input->getArgument('path');

        $path = $this->getPath();

        if (! file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $this->verifyMigrationDirectory($path);

        $path = realpath($path);

        $className = $input->getArgument('name');

        if (! Util::isValidPhinxClassName($className)) {
            throw new \InvalidArgumentException(sprintf('The seed class name "%s" is invalid. Please use CamelCase format', $className));
        }

        // Compute the file path
        $filePath = $path.\DIRECTORY_SEPARATOR.$className.'.php';

        if (is_file($filePath)) {
            throw new \InvalidArgumentException(sprintf('The file "%s" already exists', basename($filePath)));
        }

        // inject the class names appropriate to this seeder
        $contents = file_get_contents($this->getTemplate());
        $classes = [
            'SeederClass' => $className,
        ];
        $contents = strtr($contents, $classes);

        if (false === file_put_contents($filePath, $contents)) {
            throw new \RuntimeException(sprintf('The file "%s" could not be written to', $path));
        }

        $output->writeln('<info>created</info> .'.str_replace(getcwd(), '', $filePath));
    }

    protected function getPath()
    {
        return $this->path ?: config('tenancy.seed_path');
    }
}
