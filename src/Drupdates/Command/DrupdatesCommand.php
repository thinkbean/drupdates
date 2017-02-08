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

        // Use alias options if passed in
        $aliases = $input->getOption('aliases');
        if (isset($aliases)) {
            $config['aliases'] = explode(',', $aliases);
        }

        // Fail if no aliases were passed in or defined in config.
        if (!isset($config['aliases']) || count($config['aliases']) === 0) {
            echo ' Config does not contain any aliases.' . PHP_EOL;
            $output->writeln('<error>Config does not contain any aliases.</error>');
            exit(1);
        }

        // Test all alises exist
        $availableAliases = DrupalUtil::GetAvailableAliases();
        foreach ($config['aliases'] as $alias) {
            if (!in_array($alias, $availableAliases)) {
                $output->writeln('<error>Alias not available: '.$alias.'</error>');
                exit(1);
            }
        }

        $securityOnly = $input->getOption('security-only');
        $format = $input->getOption('format') === 'json' ? 'json' : null;
        $jsonOutput = [];

        foreach ($config['aliases'] as $alias) {
            if ($format === 'json') {
                $jsonOutput[$alias] = [];
            } else {
                $output->writeln($alias);
            }

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
                if ($format !== 'json') {
                    $output->writeln('  <info> No updates available. </info>');
                }
                continue;
            }

            $rows = [];
            if ($format !== 'json') {
                $table = new Table($output);
                $table->setHeaders([
                    'Module',
                    'Current',
                    'Recommended',
                    'Latest'
                ]);
            }

            foreach ($updates as $module) {
                if ($format === 'json') {
                    $jsonOutput[$alias][] = $module;
                } else {
                    $rows[] = [
                        $module['name'],
                        $module['security'] ? '<error> ' . $module['existing'] . ' </error> ' : '<info> ' . $module['existing'] . ' </info> ',
                        '<question> ' . $module['recommended'] . ' </question> ',
                        $module['latest']
                    ];
                }
            }

            if ($format !== 'json') {
                $table->setRows($rows);
                $table->setColumnWidths([30, 20, 20, 20]);
                $table->render();
            }
        }

        if ($format === 'json') {
            $output->write(json_encode($jsonOutput));
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
