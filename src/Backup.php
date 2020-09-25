<?php

namespace Dcat\Admin\Extension\Backup;

use Dcat\Admin\Extension;
use Spatie\Backup\Commands\ListCommand;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatus;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;

class Backup extends Extension
{
    const NAME = 'backup';

    protected $serviceProvider = BackupServiceProvider::class;

    protected $composer = __DIR__.'/../composer.json';

    protected $assets = __DIR__.'/../resources/assets';

    protected $views = __DIR__.'/../resources/views';

//    protected $lang = __DIR__.'/../resources/lang';

    protected $menu = [
        'title' => 'Backup',
        'path'  => 'backup',
        'icon'  => 'fa-database',
    ];

    public function getExists()
    {
        $config = [
            [
                'name'          => env('APP_NAME'),
                'disks'         => ['local'],
                'health_checks' => [
                    \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class          => 1,
                    \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 5000,
                ],
            ],
        ];
        
        $statuses = BackupDestinationStatusFactory::createForMonitorConfig($config);

        $listCommand = new ListCommand();

        $rows = $statuses->map(function (BackupDestinationStatus $backupDestinationStatus) use ($listCommand) {
            return $listCommand->convertToRow($backupDestinationStatus);
        })->all();

        foreach ($statuses as $index => $status) {
            $name = $status->backupDestination()->backupName();

            $files = array_map('basename', $status->backupDestination()->disk()->allFiles($name));

            $rows[$index]['files'] = array_slice(array_reverse($files), 0, 30);
        }

        return $rows;
    }
}
