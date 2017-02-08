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
                'existing' => $module['existing_version'],
                'recommended' => $module['recommended'],
                'latest' => $module['latest_version']
            ];
        }

        return $updates;
    }
}
