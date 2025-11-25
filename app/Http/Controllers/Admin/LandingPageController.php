<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    /**
     * Display landing page preview.
     */
    public function index(): View
    {
        $settings = $this->getSettings();
        return view('admin.landing-page.index', compact('settings'));
    }

    /**
     * Show the form for editing landing page.
     */
    public function edit(): View
    {
        $settings = $this->getSettings();
        return view('admin.landing-page.edit', compact('settings'));
    }

    /**
     * Update landing page settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
            'hero_image' => ['nullable', 'image', 'max:2048'],
            'hero_button_text' => ['nullable', 'string', 'max:50'],
            'hero_button_link' => ['nullable', 'string', 'max:255'],
            'features_title' => ['nullable', 'string', 'max:255'],
            'features_subtitle' => ['nullable', 'string', 'max:500'],
            'features' => ['nullable', 'array'],
            'features.*.title' => ['required_with:features', 'string', 'max:255'],
            'features.*.description' => ['nullable', 'string', 'max:500'],
            'features.*.icon' => ['nullable', 'string', 'max:50'],
            'about_title' => ['nullable', 'string', 'max:255'],
            'about_content' => ['nullable', 'string', 'max:2000'],
            'about_image' => ['nullable', 'image', 'max:2048'],
            'cta_title' => ['nullable', 'string', 'max:255'],
            'cta_subtitle' => ['nullable', 'string', 'max:500'],
            'cta_button_text' => ['nullable', 'string', 'max:50'],
            'cta_button_link' => ['nullable', 'string', 'max:255'],
        ]);

        $settings = $this->getSettings();

        // Handle hero image upload
        if ($request->hasFile('hero_image')) {
            if ($settings['hero_image'] ?? null) {
                Storage::disk('public')->delete($settings['hero_image']);
            }
            $settings['hero_image'] = $request->file('hero_image')->store('landing-page', 'public');
        }

        // Handle about image upload
        if ($request->hasFile('about_image')) {
            if ($settings['about_image'] ?? null) {
                Storage::disk('public')->delete($settings['about_image']);
            }
            $settings['about_image'] = $request->file('about_image')->store('landing-page', 'public');
        }

        // Update settings
        $settings['hero_title'] = $request->hero_title ?? $settings['hero_title'] ?? '';
        $settings['hero_subtitle'] = $request->hero_subtitle ?? $settings['hero_subtitle'] ?? '';
        $settings['hero_button_text'] = $request->hero_button_text ?? $settings['hero_button_text'] ?? '';
        $settings['hero_button_link'] = $request->hero_button_link ?? $settings['hero_button_link'] ?? '';
        $settings['features_title'] = $request->features_title ?? $settings['features_title'] ?? '';
        $settings['features_subtitle'] = $request->features_subtitle ?? $settings['features_subtitle'] ?? '';
        $settings['features'] = $request->features ?? $settings['features'] ?? [];
        $settings['about_title'] = $request->about_title ?? $settings['about_title'] ?? '';
        $settings['about_content'] = $request->about_content ?? $settings['about_content'] ?? '';
        $settings['cta_title'] = $request->cta_title ?? $settings['cta_title'] ?? '';
        $settings['cta_subtitle'] = $request->cta_subtitle ?? $settings['cta_subtitle'] ?? '';
        $settings['cta_button_text'] = $request->cta_button_text ?? $settings['cta_button_text'] ?? '';
        $settings['cta_button_link'] = $request->cta_button_link ?? $settings['cta_button_link'] ?? '';

        // Save to file or database (using file for simplicity)
        Storage::disk('local')->put('landing-page-settings.json', json_encode($settings, JSON_PRETTY_PRINT));

        return redirect()->route('admin.landing-page.index')
            ->with('success', 'Landing page berhasil diperbarui.');
    }

    /**
     * Get landing page settings.
     */
    protected function getSettings(): array
    {
        $defaultSettings = [
            'hero_title' => 'Selamat Datang di Sipilku',
            'hero_subtitle' => 'Platform terpercaya untuk produk dan jasa teknik sipil',
            'hero_image' => null,
            'hero_button_text' => 'Jelajahi Sekarang',
            'hero_button_link' => route('products.index'),
            'features_title' => 'Mengapa Pilih Kami?',
            'features_subtitle' => 'Kami menyediakan solusi terbaik untuk kebutuhan teknik sipil Anda',
            'features' => [
                [
                    'title' => 'Produk Berkualitas',
                    'description' => 'Produk digital dan fisik berkualitas tinggi',
                    'icon' => 'check-circle',
                ],
                [
                    'title' => 'Jasa Profesional',
                    'description' => 'Tim profesional siap membantu proyek Anda',
                    'icon' => 'users',
                ],
                [
                    'title' => 'Harga Terjangkau',
                    'description' => 'Harga kompetitif dengan kualitas terjamin',
                    'icon' => 'currency-dollar',
                ],
            ],
            'about_title' => 'Tentang Kami',
            'about_content' => 'Sipilku adalah platform terpercaya yang menghubungkan pembeli dengan seller produk dan jasa teknik sipil berkualitas.',
            'about_image' => null,
            'cta_title' => 'Siap Memulai?',
            'cta_subtitle' => 'Bergabunglah dengan kami sekarang dan dapatkan akses ke produk dan jasa terbaik',
            'cta_button_text' => 'Daftar Sekarang',
            'cta_button_link' => route('register'),
        ];

        if (Storage::disk('local')->exists('landing-page-settings.json')) {
            $savedSettings = json_decode(Storage::disk('local')->get('landing-page-settings.json'), true);
            return array_merge($defaultSettings, $savedSettings);
        }

        return $defaultSettings;
    }
}
