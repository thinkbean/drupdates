<?php

namespace Drupdates\Util;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DrupalUtil
{
    public static function GetUpdates($alias, $securityOnly = false)
    {
        $process = new Process('drush @'.$alias.' pm-updatestatus --format=json' . ($securityOnly ? ' --security-only' : ''));

        try {
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        } catch(\Exception $e) {
            throw $e;
        }

        $stdout = $process->getOutput();
        $data = json_decode(strstr($stdout, '{'), TRUE);

        if (!is_array($data) || count($data) === 0) {
            return [];
        }

        $updates = [];
        foreach ($data as $module) {
            $updates[] = [
                'name' => $module['name'],
                'existing' => isset($module['existing_version']) ? $module['existing_version'] : '',
                'recommended' => isset($module['recommended']) ? $module['recommended'] : '',
                'latest' => isset($module['latest_version']) ? $module['latest_version'] : '',
                'security' => isset($module['security updates']) && count($module['security updates']) > 0
            ];
        }

        return $updates;
    }

    public static function GetAvailableAliases()
    {
        $process = new Process('drush sa --format=json');
        try {
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
        } catch(\Exception $e) {
            throw $e;
        }

        $stdout = $process->getOutput();
        $data = json_decode($stdout, TRUE);

        $aliases = [];
        if (is_array($data)) {
            $items = array_keys($data);
            foreach ($items as $item) {
                $aliases[] = substr($item, 1);
            }
        }

        return $aliases;
    }
}
