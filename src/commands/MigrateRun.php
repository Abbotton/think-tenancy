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
use think\migration\command\migrate\Run as BaseRun;
use think\tenancy\events\DatabaseMigrated;
use think\tenancy\events\MigratingDatabase;
use think\tenancy\services\ResetService;

class MigrateRun extends BaseRun
{
    private $tenant;

    private $path = '';

    public function getPath()
    {
        $this->tenant = tenancy()->find($this->input->getOption('tenant'));
        // TODO 判断租户状态是否正常
        ResetService::resetDatabase($this->tenant);

        return $this->path ?: config('tenancy.migration_path');
    }

    protected function configure(): void
    {
        $this->setName('tenants:migrate:run')
            ->setDescription('多租户迁移数据库')
            ->addArgument('path', InputArgument::OPTIONAL, '指定迁移文件加载目录')
            ->addOption('tenant', '', InputOption::VALUE_REQUIRED, '租户标识,仅支持子域名')
            ->addOption('target', 't', InputOption::VALUE_REQUIRED, 'The version number to migrate to')
            ->addOption('date', 'd', InputOption::VALUE_REQUIRED, 'The date to migrate to')
            ->setHelp(
                <<<'EOT'
The <info>migrate:run</info> command runs all available migrations, optionally up to a specific version

<info>php think migrate:run</info>
<info>php think migrate:run -t 20110103081132</info>
<info>php think migrate:run -d 20110103</info>
<info>php think migrate:run -v</info>

EOT
            );
    }

    protected function execute(Input $input, Output $output): void
    {
        event(new MigratingDatabase($this->tenant));

        $this->path = $input->getArgument('path');
        $version = $input->getOption('target');
        $date = $input->getOption('date');

        // run the migrations
        $start = microtime(true);
        if (null !== $date) {
            $this->migrateToDateTime(new \DateTime($date));
        } else {
            $this->migrate($version);
        }
        $end = microtime(true);

        $output->writeln('');
        $output->writeln('<comment>All Done. Took '.sprintf('%.4fs', $end - $start).'</comment>');

        event(new DatabaseMigrated($this->tenant));
    }
}
