<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SystemConfigController extends Controller
{
    /**
     * Display system configuration page.
     */
    public function index(Request $request): View
    {
        $group = $request->get('group', 'general');
        
        $settings = Setting::where('group', $group)->get();
        $groups = Setting::select('group')->distinct()->pluck('group');

        return view('admin.settings.index', compact('settings', 'groups', 'group'));
    }

    /**
     * Update system settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'settings' => ['required', 'array'],
            'settings.*.key' => ['required', 'string'],
            'settings.*.value' => ['nullable'],
            'settings.*.type' => ['required', 'string', 'in:string,integer,float,boolean,json,array,text'],
        ]);

        foreach ($request->settings as $settingData) {
            $setting = Setting::where('key', $settingData['key'])->first();
            
            if ($setting) {
                $value = $settingData['value'] ?? null;
                
                // Prepare value based on type
                if ($setting->type === 'boolean' || $setting->type === 'bool') {
                    $value = $request->has("settings.{$settingData['key']}.value") ? '1' : '0';
                } elseif ($setting->type === 'json' || $setting->type === 'array') {
                    $value = is_string($value) ? $value : json_encode($value);
                }

                $setting->update([
                    'value' => $value,
                    'description' => $settingData['description'] ?? $setting->description,
                ]);

                // Clear cache
                $setting->clearCache();
            }
        }

        return redirect()->route('admin.settings.index', ['group' => $request->group ?? 'general'])
            ->with('success', 'Pengaturan berhasil diperbarui.');
    }

    /**
     * Store a new setting.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:255', 'unique:settings,key'],
            'value' => ['nullable'],
            'type' => ['required', 'string', 'in:string,integer,float,boolean,json,array,text'],
            'group' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_public' => ['boolean'],
        ]);

        Setting::create($validated);

        return redirect()->route('admin.settings.index', ['group' => $validated['group']])
            ->with('success', 'Pengaturan berhasil ditambahkan.');
    }

    /**
     * Delete a setting.
     */
    public function destroy(Setting $setting): RedirectResponse
    {
        $group = $setting->group;
        $setting->clearCache();
        $setting->delete();

        return redirect()->route('admin.settings.index', ['group' => $group])
            ->with('success', 'Pengaturan berhasil dihapus.');
    }
}
