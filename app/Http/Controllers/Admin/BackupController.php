<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BackupController extends Controller
{
    /**
     * Display backup management page.
     */
    public function index(): View
    {
        // Get backup files
        $backupFiles = [];
        $backupPath = storage_path('app/backups');
        
        if (is_dir($backupPath)) {
            $files = glob($backupPath . '/*.zip');
            foreach ($files as $file) {
                $backupFiles[] = [
                    'name' => basename($file),
                    'size' => filesize($file),
                    'created_at' => date('Y-m-d H:i:s', filemtime($file)),
                ];
            }
            
            // Sort by created_at descending
            usort($backupFiles, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
        }

        // Get disk space info
        $diskSpace = [
            'total' => disk_total_space(storage_path()),
            'free' => disk_free_space(storage_path()),
            'used' => disk_total_space(storage_path()) - disk_free_space(storage_path()),
        ];

        return view('admin.backups.index', compact('backupFiles', 'diskSpace'));
    }

    /**
     * Create a new backup.
     */
    public function create(Request $request): RedirectResponse
    {
        $request->validate([
            'backup_type' => ['required', 'string', 'in:full,database,files'],
        ]);

        try {
            $backupType = $request->backup_type;
            
            // Create backup directory if not exists
            $backupPath = storage_path('app/backups');
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            // Run backup command
            if ($backupType === 'full') {
                Artisan::call('backup:run');
            } elseif ($backupType === 'database') {
                Artisan::call('backup:run', ['--only-db' => true]);
            } elseif ($backupType === 'files') {
                Artisan::call('backup:run', ['--only-files' => true]);
            }

            return redirect()->route('admin.backups.index')
                ->with('success', 'Backup berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->route('admin.backups.index')
                ->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file.
     */
    public function download(string $filename): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filePath = storage_path('app/backups/' . $filename);
        
        if (!file_exists($filePath)) {
            abort(404, 'Backup file not found.');
        }

        return response()->download($filePath);
    }

    /**
     * Delete a backup file.
     */
    public function destroy(string $filename): RedirectResponse
    {
        $filePath = storage_path('app/backups/' . $filename);
        
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return redirect()->route('admin.backups.index')
            ->with('success', 'Backup berhasil dihapus.');
    }

    /**
     * Restore from backup.
     */
    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'backup_file' => ['required', 'string'],
        ]);

        try {
            $filePath = storage_path('app/backups/' . $request->backup_file);
            
            if (!file_exists($filePath)) {
                return redirect()->back()
                    ->with('error', 'Backup file tidak ditemukan.');
            }

            // Run restore command (this would need a custom command)
            // Artisan::call('backup:restore', ['file' => $request->backup_file]);

            return redirect()->route('admin.backups.index')
                ->with('success', 'Restore berhasil dilakukan. (Note: Restore functionality needs to be implemented)');
        } catch (\Exception $e) {
            return redirect()->route('admin.backups.index')
                ->with('error', 'Gagal restore: ' . $e->getMessage());
        }
    }
}
