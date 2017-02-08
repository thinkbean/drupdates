<?php

namespace Drupdates\Command;

use Drupdates\Util\DrupalUtil;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DrupdatesCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('drupal:updates')
            ->setDescription('Retrieves available updates for the configured aliases.')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = getenv("HOME").'/.thinkbean/drupdates.json';
        if (!file_exists($file)) {
            echo ' Config file does not exist: `'.$file.'`' . PHP_EOL;
            exit(1);
        }

        $config = json_decode(file_get_contents($file), true);
        if (!isset($config['aliases']) || count($config['aliases']) === 0) {
            echo ' Config does not contain any aliases.' . PHP_EOL;
            exit(1);
        }

        foreach ($config['aliases'] as $alias) {

            $output->writeln(' ');
            $output->writeln($alias);

            try {
                $updates = DrupalUtil::GetUpdates($alias, true);

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
                    $module['name'].'</error>',
                    '<error> '.$module['existing_version'].' </error> ',
                    '<question> '.$module['recommended'].' </question> ',
                    $module['latest_version']
                ];
            }

            $table->setRows($rows);
            $table->setColumnWidths(array(35, 15, 15, 15));
            $table->render();
        }
    }
}
