<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Support\Facades\File;

class RequirementDownloadController extends Controller
{
    public function downloadJenisProtokol()
    {
        $dir = storage_path('app/private/jenis_protokol');
        $zipFileName = 'Jenis_Protokol_Pengajuan.zip';
        $zipPath = storage_path('app/private/' . $zipFileName);

        if (!File::exists($dir)) {
            abort(404, 'Folder tidak ditemukan.');
        }

        $files = File::files($dir);

        if (empty($files)) {
            return back()->with('error', 'Tidak ada file di dalam folder.');
        }

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($files as $file) {
                $zip->addFile($file->getPathname(), $file->getFilename());
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
