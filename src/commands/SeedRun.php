<?php

declare(strict_types=1);

/*
 * ThinkPHP多租户扩展.
 *
 * @author Abbotton <uctoo@foxmail.com>
 */

namespace think\tenancy\commands;

use think\console\Input;
use think\console\input\Argument as InputArgument;
use think\console\input\Option as InputOption;
use think\console\Output;
use think\migration\command\seed\Run as BaseRun;
use think\tenancy\events\DatabaseSeeded;
use think\tenancy\events\SeedingDatabase;
use think\tenancy\services\ResetService;

class SeedRun extends BaseRun
{
    private $tenant;

    private $path;

    public function getPath()
    {
        $this->tenant = tenancy()->find($this->input->getOption('tenant'));
        // TODO 判断租户状态是否正常
        ResetService::resetDatabase($this->tenant);

        return $this->path ?: config('tenancy.seed_path');
    }

    protected function configure(): void
    {
        $this->setName('tenants:seed:run')
            ->setDescription('多租户运行数据库填充')
            ->addArgument('path', InputArgument::OPTIONAL, '指定数据填充文件存放目录')
            ->addOption('tenant', '', InputOption::VALUE_REQUIRED, '租户标识,仅支持子域名')
            ->addOption('seed', 's', InputOption::VALUE_REQUIRED, 'What is the name of the seeder?')
            ->setHelp(
                <<<'EOT'
                The <info>seed:run</info> command runs all available or individual seeders

<info>php think seed:run</info>
<info>php think seed:run -s UserSeeder</info>
<info>php think seed:run -v</info>

EOT
            );
    }

    /**
     * Run database seeders.
     */
    protected function execute(Input $input, Output $output): void
    {
        event(new SeedingDatabase($this->tenant));

        $this->path = $input->getArgument('path');
        $seed = $input->getOption('seed');
        $start = microtime(true);
        $this->seed($seed);
        $end = microtime(true);

        $output->writeln('<comment>All Done. Took '.sprintf('%.4fs', $end - $start).'</comment>');

        event(new DatabaseSeeded($this->tenant));
    }
}
