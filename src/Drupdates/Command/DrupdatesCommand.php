<?php

namespace Drupdates\Command;

use Drupdates\Util\DrupalUtil;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DrupdatesCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('drupal:updates')
            ->setDescription('Retrieves available updates for the configured aliases.')
            ->addOption('security-only', null, InputOption::VALUE_NONE, 'Limit results to security updates only.')
            ->addOption('aliases', null, InputOption::VALUE_OPTIONAL, 'Use a comma separate list of aliases instead of config.')
            ->addOption('format', null, InputOption::VALUE_OPTIONAL, 'Format results. Supported formats: json')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $this->getConfigFilePath();
        if (!file_exists($file)) {
            $this->initializeConfig();

            $output->writeln('');
            $output->writeln('  Your config file has been initialized.');
            $output->writeln('  Please update the aliases array in the file below.');
            $output->writeln('  <info>'.$file.'</info>');
            $output->writeln('');
            exit(1);
        }

        $config = json_decode(file_get_contents($file), true);
        if (!isset($config['aliases']) || count($config['aliases']) === 0) {
            echo ' Config does not contain any aliases.' . PHP_EOL;
            exit(1);
        }

        $aliases = $input->getOption('aliases');
        if (isset($aliases)) {
            $config['aliases'] = explode(',', $aliases);
        }

        $securityOnly = $input->getOption('security-only');

        foreach ($config['aliases'] as $alias) {

            $output->writeln($alias);
            try {
                $updates = DrupalUtil::GetUpdates($alias, $securityOnly);

            } catch(ProcessFailedException $e) {
                $output->writeln('  <error>'.$e->getMessage().'</error>');
                continue;
            } catch(\Exception $e) {
                $output->writeln('  <error>'.$e->getMessage().'</error>');
                continue;
            }

            if (!is_array($updates) || count($updates) === 0) {
                $output->writeln('  <info> No updates available. </info>');
                continue;
            }

            $table = new Table($output);
            $table->setHeaders(['Module', 'Current', 'Recommended', 'Latest']);
            $rows = [];

            foreach ($updates as $module) {
                $rows[] = [
                    $module['name'],
                    $module['security'] ? '<error> '.$module['existing'].' </error> ' : '<info> '.$module['existing'].' </info> ',
                    '<question> '.$module['recommended'].' </question> ',
                    $module['latest']
                ];
            }

            $table->setRows($rows);
            $table->setColumnWidths(array(30, 20, 20, 20));
            $table->render();
        }
    }

    private function getConfigFilePath()
    {
        return getenv('HOME').'/.thinkbean/drupdates.json';
    }

    private function initializeConfig()
    {
        $str =<<<EOF
{
  "aliases": [
    "example.alias"
  ]
}
EOF;
        $file = $this->getConfigFilePath();
        file_put_contents($file, $str);
    }
}
