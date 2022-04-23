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
use think\migration\command\migrate\Status as BaseStatus;
use think\tenancy\services\ResetService;

class MigrateStatus extends BaseStatus
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
        $this->setName('tenants:migrate:status')
            ->setDescription('多租户显示迁移状态')
            ->addArgument('path', InputArgument::OPTIONAL, '指定迁移文件加载目录')
            ->addOption('tenant', '', InputOption::VALUE_REQUIRED, '租户标识,仅支持子域名')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'The output format: text or json. Defaults to text.')
            ->setHelp(
                <<<'EOT'
The <info>migrate:status</info> command prints a list of all migrations, along with their current status

<info>php think migrate:status</info>
<info>php think migrate:status -f json</info>
EOT
            );
    }

    /**
     * Show the migration status.
     *
     * @return null|int|void
     */
    protected function execute(Input $input, Output $output)
    {
        $this->path = $input->getArgument('path');
        $format = $input->getOption('format');

        if (null !== $format) {
            $output->writeln('<info>using format</info> '.$format);
        }

        // print the status
        return $this->printStatus($format);
    }
}
