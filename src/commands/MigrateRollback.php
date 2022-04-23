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
use think\migration\command\migrate\Rollback as BaseRollback;
use think\tenancy\services\ResetService;

class MigrateRollback extends BaseRollback
{
    private $path = '';

    public function getPath()
    {
        $tenant = tenancy()->find($this->input->getOption('tenant'));
        // TODO 判断租户状态是否正常
        ResetService::resetDatabase($tenant);

        return $this->path ?: config('tenancy.migration_path');
    }

    protected function configure(): void
    {
        $this->setName('tenants:migrate:rollback')
            ->setDescription('多租户回滚最后一次或特定迁移')
            ->addArgument('path', InputArgument::OPTIONAL, '指定迁移文件加载目录')
            ->addOption('tenant', '', InputOption::VALUE_REQUIRED, '租户标识,仅支持子域名')
            ->addOption('target', 't', InputOption::VALUE_REQUIRED, 'The version number to rollback to')
            ->addOption('date', 'd', InputOption::VALUE_REQUIRED, 'The date to rollback to')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force rollback to ignore breakpoints')
            ->setHelp(
                <<<'EOT'
The <info>migrate:rollback</info> command reverts the last migration, or optionally up to a specific version

<info>php think migrate:rollback</info>
<info>php think migrate:rollback -t 20111018185412</info>
<info>php think migrate:rollback -d 20111018</info>
<info>php think migrate:rollback -v</info>

EOT
            );
    }

    /**
     * Rollback the migration.
     *
     * @throws \Exception
     */
    protected function execute(Input $input, Output $output): void
    {
        $this->path = $input->getArgument('path');
        $version = $input->getOption('target');
        $date = $input->getOption('date');
        $force = (bool)$input->getOption('force');

        // rollback the specified environment
        $start = microtime(true);
        if (null !== $date) {
            $this->rollbackToDateTime(new \DateTime($date), $force);
        } else {
            $this->rollback($version, $force);
        }
        $end = microtime(true);

        $output->writeln('');
        $output->writeln('<comment>All Done. Took '.sprintf('%.4fs', $end - $start).'</comment>');
    }
}
