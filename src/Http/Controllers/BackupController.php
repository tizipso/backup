<?php

namespace Dcat\Admin\Extension\Backup\Http\Controllers;

use Dcat\Admin\Layout\Content;
use Dcat\Admin\Extension\Backup\Backup;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


class BackupController extends Controller
{
    public function index(Content $content)
    {
        $backup = new Backup();

        return $content
            ->title('Backup')
            ->body(view('backup::index', [
                'backups' => $backup->getExists(),
            ]));
    }

    public function run()
    {
        try {
            ini_set('max_execution_time', 300);

            // start the backup process
            Artisan::call('backup:run');

            $output = Artisan::output();

            return response()->json([
                'status'  => true,
                'message' => $output,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function download(Request $request)
    {
        $disk = $request->get('disk');
        $file = $request->get('file');

        $storage = Storage::disk($disk);

        $fullPath = $storage->getDriver()->getAdapter()->applyPathPrefix($file);

        if (File::isFile($fullPath)) {
            return response()->download($fullPath);
        }

        return response('', 404);
    }

    public function delete(Request $request)
    {
        $disk = Storage::disk($request->get('disk'));
        $file = $request->get('file');

        if ($disk->exists($file)) {
            $disk->delete($file);

            return response()->json([
                'status'  => true,
                'message' => trans('admin.delete_succeeded'),
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => trans('admin.delete_failed'),
        ]);
    }
}
