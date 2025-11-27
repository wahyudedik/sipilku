<?php

namespace App\Services;

use App\Models\FactoryType;

class FactoryTypeProductService
{
    /**
     * Get factory type-specific product configurations.
     */
    public function getConfig(string $factoryTypeSlug): array
    {
        return match(strtolower($factoryTypeSlug)) {
            'beton', 'concrete' => $this->getBetonConfig(),
            'bata', 'brick' => $this->getBataConfig(),
            'genting', 'roof-tile', 'tile' => $this->getGentingConfig(),
            'baja', 'steel' => $this->getBajaConfig(),
            'precast' => $this->getPrecastConfig(),
            'keramik', 'granit', 'ceramic', 'granite' => $this->getKeramikConfig(),
            'kayu', 'wood' => $this->getKayuConfig(),
            default => $this->getDefaultConfig(),
        };
    }

    /**
     * Get product categories for factory type.
     */
    public function getProductCategories(string $factoryTypeSlug): array
    {
        return match(strtolower($factoryTypeSlug)) {
            'beton', 'concrete' => [
                'ready-mix' => 'Ready Mix',
                'precast' => 'Precast',
                'mobil-molen' => 'Mobil Molen',
                'beton-cor' => 'Beton Cor',
            ],
            'bata', 'brick' => [
                'bata-merah' => 'Bata Merah',
                'bata-putih' => 'Bata Putih',
                'bata-press' => 'Bata Press',
                'bata-expose' => 'Bata Expose',
                'bata-hollow' => 'Bata Hollow',
            ],
            'genting', 'roof-tile', 'tile' => [
                'genting-tanah-liat' => 'Genting Tanah Liat',
                'genting-beton' => 'Genting Beton',
                'genting-metal' => 'Genting Metal',
                'genting-keramik' => 'Genting Keramik',
            ],
            'baja', 'steel' => [
                'iwf' => 'IWF (I-Beam)',
                'h-beam' => 'H-Beam',
                'unp' => 'UNP (Channel)',
                'cnp' => 'CNP (Channel)',
                'plat' => 'Plat Baja',
                'pipa' => 'Pipa Baja',
                'wire-mesh' => 'Wire Mesh',
            ],
            'precast' => [
                'panel' => 'Panel',
                'kolom' => 'Kolom',
                'balok' => 'Balok',
                'plat' => 'Plat',
                'dinding' => 'Dinding',
                'custom' => 'Custom Order',
            ],
            'keramik', 'granit', 'ceramic', 'granite' => [
                'keramik-lantai' => 'Keramik Lantai',
                'keramik-dinding' => 'Keramik Dinding',
                'granit' => 'Granit',
                'marmer' => 'Marmer',
            ],
            'kayu', 'wood' => [
                'balok' => 'Balok',
                'papan' => 'Papan',
                'triplek' => 'Triplek',
                'multiplek' => 'Multiplek',
                'kaso' => 'Kaso',
                'reng' => 'Reng',
            ],
            default => [],
        };
    }

    /**
     * Get default units for factory type.
     */
    public function getDefaultUnits(string $factoryTypeSlug): array
    {
        return match(strtolower($factoryTypeSlug)) {
            'beton', 'concrete' => ['m3', 'mobil'],
            'bata', 'brick' => ['pcs', 'm3', 'kubik'],
            'genting', 'roof-tile', 'tile' => ['pcs', 'm2'],
            'baja', 'steel' => ['kg', 'ton'],
            'precast' => ['unit', 'pcs', 'm2', 'm3'],
            'keramik', 'granit', 'ceramic', 'granite' => ['m2', 'box', 'pcs'],
            'kayu', 'wood' => ['m3', 'm2', 'pcs'],
            default => ['pcs', 'unit'],
        };
    }

    /**
     * Get specifications template for factory type.
     */
    public function getSpecificationsTemplate(string $factoryTypeSlug): array
    {
        return match(strtolower($factoryTypeSlug)) {
            'beton', 'concrete' => [
                'Slump' => '',
                'Agregat Maksimum' => '',
                'Air Content' => '',
                'Cement Type' => '',
            ],
            'bata', 'brick' => [
                'Ukuran (PxLxT)' => '',
                'Berat' => '',
                'Kuat Tekan' => '',
                'Absorpsi Air' => '',
            ],
            'genting', 'roof-tile', 'tile' => [
                'Ukuran (PxL)' => '',
                'Berat' => '',
                'Ketebalan' => '',
                'Warna' => '',
            ],
            'baja', 'steel' => [
                'Dimensi' => '',
                'Berat per Meter' => '',
                'Tebal' => '',
                'Standard' => '',
            ],
            'precast' => [
                'Dimensi (PxLxT)' => '',
                'Berat' => '',
                'Besi Tulangan' => '',
                'Ketebalan' => '',
            ],
            'keramik', 'granit', 'ceramic', 'granite' => [
                'Ukuran (PxL)' => '',
                'Ketebalan' => '',
                'Motif' => '',
                'Finish' => '',
            ],
            'kayu', 'wood' => [
                'Ukuran (PxLxT)' => '',
                'Jenis Kayu' => '',
                'Grade' => '',
                'Kadar Air' => '',
            ],
            default => [],
        };
    }

    /**
     * Pabrik Beton Configuration
     */
    private function getBetonConfig(): array
    {
        return [
            'name' => 'Pabrik Beton',
            'default_units' => ['m3', 'mobil'],
            'quality_grades' => [
                'K-100' => 'K-100 (fc\' = 8.3 MPa)',
                'K-125' => 'K-125 (fc\' = 10.4 MPa)',
                'K-150' => 'K-150 (fc\' = 12.2 MPa)',
                'K-175' => 'K-175 (fc\' = 14.5 MPa)',
                'K-200' => 'K-200 (fc\' = 16.9 MPa)',
                'K-225' => 'K-225 (fc\' = 19.3 MPa)',
                'K-250' => 'K-250 (fc\' = 21.7 MPa)',
                'K-275' => 'K-275 (fc\' = 24.0 MPa)',
                'K-300' => 'K-300 (fc\' = 26.4 MPa)',
                'K-350' => 'K-350 (fc\' = 30.5 MPa)',
                'K-400' => 'K-400 (fc\' = 33.2 MPa)',
            ],
            'product_categories' => $this->getProductCategories('beton'),
            'specifications_template' => $this->getSpecificationsTemplate('beton'),
            'pricing_notes' => 'Harga dapat per m3 atau per mobil molen',
        ];
    }

    /**
     * Pabrik Bata Configuration
     */
    private function getBataConfig(): array
    {
        return [
            'name' => 'Pabrik Bata',
            'default_units' => ['pcs', 'm3', 'kubik'],
            'quality_grades' => [
                'Merah' => 'Bata Merah',
                'Press' => 'Bata Press',
                'Expose' => 'Bata Expose',
                'Hollow' => 'Bata Hollow',
            ],
            'product_categories' => $this->getProductCategories('bata'),
            'specifications_template' => $this->getSpecificationsTemplate('bata'),
            'pricing_notes' => 'Harga dapat per pcs atau per kubik (m3)',
        ];
    }

    /**
     * Pabrik Genting Configuration
     */
    private function getGentingConfig(): array
    {
        return [
            'name' => 'Pabrik Genting',
            'default_units' => ['pcs', 'm2'],
            'quality_grades' => [
                'Standar' => 'Genting Standar',
                'Premium' => 'Genting Premium',
                'Metal' => 'Genting Metal',
            ],
            'product_categories' => $this->getProductCategories('genting'),
            'specifications_template' => $this->getSpecificationsTemplate('genting'),
            'pricing_notes' => 'Harga dapat per pcs atau per m2',
        ];
    }

    /**
     * Pabrik Baja Configuration
     */
    private function getBajaConfig(): array
    {
        return [
            'name' => 'Pabrik Baja',
            'default_units' => ['kg', 'ton'],
            'quality_grades' => [
                'BJTP' => 'Baja BJTP',
                'BJTS' => 'Baja BJTS',
                'BJTD' => 'Baja BJTD',
            ],
            'product_categories' => $this->getProductCategories('baja'),
            'specifications_template' => $this->getSpecificationsTemplate('baja'),
            'pricing_notes' => 'Harga per kg atau per ton',
        ];
    }

    /**
     * Pabrik Precast Configuration
     */
    private function getPrecastConfig(): array
    {
        return [
            'name' => 'Pabrik Precast',
            'default_units' => ['unit', 'pcs', 'm2', 'm3'],
            'quality_grades' => [
                'Standard' => 'Standard',
                'Premium' => 'Premium',
                'High Grade' => 'High Grade',
            ],
            'product_categories' => $this->getProductCategories('precast'),
            'specifications_template' => $this->getSpecificationsTemplate('precast'),
            'pricing_notes' => 'Harga per unit, pcs, m2, atau m3. Custom order tersedia.',
            'supports_custom_order' => true,
        ];
    }

    /**
     * Pabrik Keramik/Granit Configuration
     */
    private function getKeramikConfig(): array
    {
        return [
            'name' => 'Pabrik Keramik/Granit',
            'default_units' => ['m2', 'box', 'pcs'],
            'quality_grades' => [
                'Grade A' => 'Grade A (Premium)',
                'Grade B' => 'Grade B (Standard)',
                'Grade C' => 'Grade C (Ekonomi)',
            ],
            'product_categories' => $this->getProductCategories('keramik'),
            'specifications_template' => $this->getSpecificationsTemplate('keramik'),
            'pricing_notes' => 'Harga per m2, box, atau pcs',
        ];
    }

    /**
     * Pabrik Kayu Configuration
     */
    private function getKayuConfig(): array
    {
        return [
            'name' => 'Pabrik Kayu',
            'default_units' => ['m3', 'm2', 'pcs'],
            'quality_grades' => [
                'Grade A' => 'Grade A (Premium)',
                'Grade B' => 'Grade B (Standard)',
                'Grade C' => 'Grade C (Ekonomi)',
            ],
            'product_categories' => $this->getProductCategories('kayu'),
            'specifications_template' => $this->getSpecificationsTemplate('kayu'),
            'pricing_notes' => 'Harga per m3, m2, atau pcs',
        ];
    }

    /**
     * Default Configuration
     */
    private function getDefaultConfig(): array
    {
        return [
            'name' => 'Pabrik',
            'default_units' => ['pcs', 'unit'],
            'quality_grades' => [
                'Standard' => 'Standard',
                'Premium' => 'Premium',
                'High Grade' => 'High Grade',
            ],
            'product_categories' => [],
            'specifications_template' => [],
            'pricing_notes' => 'Harga per unit',
        ];
    }

    /**
     * Get product name suggestions based on factory type and category.
     */
    public function getProductNameSuggestions(string $factoryTypeSlug, ?string $category = null): array
    {
        $categories = $this->getProductCategories($factoryTypeSlug);
        
        if ($category && isset($categories[$category])) {
            return match(strtolower($factoryTypeSlug)) {
                'beton', 'concrete' => match($category) {
                    'ready-mix' => ['Ready Mix K-200', 'Ready Mix K-250', 'Ready Mix K-300'],
                    'precast' => ['Panel Precast', 'Kolom Precast', 'Balok Precast'],
                    'mobil-molen' => ['Mobil Molen 7 m3', 'Mobil Molen 9 m3'],
                    default => [],
                },
                'bata', 'brick' => match($category) {
                    'bata-merah' => ['Bata Merah 5x10x20', 'Bata Merah 6x12x24'],
                    'bata-press' => ['Bata Press Standar', 'Bata Press Expose'],
                    default => [],
                },
                'genting', 'roof-tile', 'tile' => match($category) {
                    'genting-tanah-liat' => ['Genting Tanah Liat Standar', 'Genting Tanah Liat Premium'],
                    'genting-beton' => ['Genting Beton Standar', 'Genting Beton Premium'],
                    default => [],
                },
                'baja', 'steel' => match($category) {
                    'iwf' => ['IWF 100', 'IWF 150', 'IWF 200'],
                    'h-beam' => ['H-Beam 200x200', 'H-Beam 300x300'],
                    'unp' => ['UNP 100', 'UNP 150', 'UNP 200'],
                    default => [],
                },
                default => [],
            };
        }

        return [];
    }
}

