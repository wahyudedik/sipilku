<?php

namespace App\Services;

use App\Models\Factory;
use App\Models\FactoryRequest;
use App\Models\FactoryType;
use App\Services\FactoryTypeProductService;

class FactoryProcurementWorkflowService
{
    protected FactoryTypeProductService $typeService;

    public function __construct(FactoryTypeProductService $typeService)
    {
        $this->typeService = $typeService;
    }

    /**
     * Get procurement workflow steps for a factory type.
     */
    public function getWorkflowSteps(string $factoryTypeSlug): array
    {
        return match(strtolower($factoryTypeSlug)) {
            'beton', 'concrete' => $this->getBetonWorkflow(),
            'bata', 'brick' => $this->getBataWorkflow(),
            'genting', 'roof-tile', 'tile' => $this->getGentingWorkflow(),
            'baja', 'steel' => $this->getBajaWorkflow(),
            'precast' => $this->getPrecastWorkflow(),
            'keramik', 'granit', 'ceramic', 'granite' => $this->getKeramikWorkflow(),
            'kayu', 'wood' => $this->getKayuWorkflow(),
            default => $this->getDefaultWorkflow(),
        };
    }

    /**
     * Pabrik Beton workflow.
     */
    private function getBetonWorkflow(): array
    {
        return [
            [
                'step' => 1,
                'name' => 'Quote Request',
                'description' => 'Contractor submits quote request with concrete specifications (grade, slump, quantity)',
                'duration' => '1-2 days',
            ],
            [
                'step' => 2,
                'name' => 'Factory Quote',
                'description' => 'Factory provides quote including ready mix price, mobil molen cost, and delivery',
                'duration' => '1-2 days',
            ],
            [
                'step' => 3,
                'name' => 'Quote Comparison',
                'description' => 'Contractor compares quotes from multiple factories (price, quality, distance)',
                'duration' => '1 day',
            ],
            [
                'step' => 4,
                'name' => 'Order Acceptance',
                'description' => 'Contractor accepts quote and confirms order',
                'duration' => '1 day',
            ],
            [
                'step' => 5,
                'name' => 'Production Schedule',
                'description' => 'Factory schedules concrete production and mobil molen dispatch',
                'duration' => '2-3 days',
            ],
            [
                'step' => 6,
                'name' => 'Delivery',
                'description' => 'Mobil molen delivers concrete to project site',
                'duration' => '1 day',
            ],
            [
                'step' => 7,
                'name' => 'Quality Check',
                'description' => 'Contractor verifies concrete quality and grade',
                'duration' => 'Same day',
            ],
        ];
    }

    /**
     * Pabrik Bata workflow.
     */
    private function getBataWorkflow(): array
    {
        return [
            [
                'step' => 1,
                'name' => 'Quote Request',
                'description' => 'Contractor requests quote for bricks (type, quantity, quality grade)',
                'duration' => '1 day',
            ],
            [
                'step' => 2,
                'name' => 'Factory Quote',
                'description' => 'Factory provides quote per pcs or per kubik',
                'duration' => '1 day',
            ],
            [
                'step' => 3,
                'name' => 'Order Acceptance',
                'description' => 'Contractor accepts quote',
                'duration' => '1 day',
            ],
            [
                'step' => 4,
                'name' => 'Production/Stock',
                'description' => 'Factory prepares order from stock or production',
                'duration' => '2-5 days',
            ],
            [
                'step' => 5,
                'name' => 'Delivery',
                'description' => 'Bricks delivered to project site',
                'duration' => '1 day',
            ],
        ];
    }

    /**
     * Pabrik Genting workflow.
     */
    private function getGentingWorkflow(): array
    {
        return [
            [
                'step' => 1,
                'name' => 'Quote Request',
                'description' => 'Contractor requests quote for roof tiles (type, size, quantity)',
                'duration' => '1 day',
            ],
            [
                'step' => 2,
                'name' => 'Factory Quote',
                'description' => 'Factory provides quote per m2 or per pcs',
                'duration' => '1 day',
            ],
            [
                'step' => 3,
                'name' => 'Order Acceptance',
                'description' => 'Contractor accepts quote',
                'duration' => '1 day',
            ],
            [
                'step' => 4,
                'name' => 'Production',
                'description' => 'Factory produces roof tiles (if custom) or prepares from stock',
                'duration' => '3-7 days',
            ],
            [
                'step' => 5,
                'name' => 'Delivery',
                'description' => 'Roof tiles delivered with careful handling',
                'duration' => '1 day',
            ],
        ];
    }

    /**
     * Pabrik Baja workflow.
     */
    private function getBajaWorkflow(): array
    {
        return [
            [
                'step' => 1,
                'name' => 'Quote Request',
                'description' => 'Contractor requests quote for steel products (IWF, H-Beam, UNP, etc.)',
                'duration' => '1 day',
            ],
            [
                'step' => 2,
                'name' => 'Factory Quote',
                'description' => 'Factory provides quote per kg or per ton',
                'duration' => '1-2 days',
            ],
            [
                'step' => 3,
                'name' => 'Order Acceptance',
                'description' => 'Contractor accepts quote',
                'duration' => '1 day',
            ],
            [
                'step' => 4,
                'name' => 'Steel Processing',
                'description' => 'Factory processes or prepares steel from stock',
                'duration' => '3-5 days',
            ],
            [
                'step' => 5,
                'name' => 'Delivery',
                'description' => 'Steel delivered using appropriate transport',
                'duration' => '1 day',
            ],
        ];
    }

    /**
     * Pabrik Precast workflow.
     */
    private function getPrecastWorkflow(): array
    {
        return [
            [
                'step' => 1,
                'name' => 'Quote Request',
                'description' => 'Contractor requests quote for precast elements (panel, kolom, balok)',
                'duration' => '1-2 days',
            ],
            [
                'step' => 2,
                'name' => 'Factory Quote',
                'description' => 'Factory provides quote including custom design if needed',
                'duration' => '2-3 days',
            ],
            [
                'step' => 3,
                'name' => 'Design Approval',
                'description' => 'Contractor reviews and approves precast design',
                'duration' => '2-3 days',
            ],
            [
                'step' => 4,
                'name' => 'Order Acceptance',
                'description' => 'Contractor accepts quote and design',
                'duration' => '1 day',
            ],
            [
                'step' => 5,
                'name' => 'Production',
                'description' => 'Factory produces precast elements',
                'duration' => '7-14 days',
            ],
            [
                'step' => 6,
                'name' => 'Curing',
                'description' => 'Precast elements undergo curing process',
                'duration' => '7-14 days',
            ],
            [
                'step' => 7,
                'name' => 'Delivery',
                'description' => 'Precast delivered using crane-equipped transport',
                'duration' => '1 day',
            ],
        ];
    }

    /**
     * Pabrik Keramik/Granit workflow.
     */
    private function getKeramikWorkflow(): array
    {
        return [
            [
                'step' => 1,
                'name' => 'Quote Request',
                'description' => 'Contractor requests quote for tiles (size, motif, grade)',
                'duration' => '1 day',
            ],
            [
                'step' => 2,
                'name' => 'Factory Quote',
                'description' => 'Factory provides quote per m2 or per box',
                'duration' => '1 day',
            ],
            [
                'step' => 3,
                'name' => 'Order Acceptance',
                'description' => 'Contractor accepts quote',
                'duration' => '1 day',
            ],
            [
                'step' => 4,
                'name' => 'Stock/Production',
                'description' => 'Factory prepares tiles from stock or production',
                'duration' => '2-5 days',
            ],
            [
                'step' => 5,
                'name' => 'Delivery',
                'description' => 'Tiles delivered with careful handling',
                'duration' => '1 day',
            ],
        ];
    }

    /**
     * Pabrik Kayu workflow.
     */
    private function getKayuWorkflow(): array
    {
        return [
            [
                'step' => 1,
                'name' => 'Quote Request',
                'description' => 'Contractor requests quote for wood products (type, grade, dimensions)',
                'duration' => '1 day',
            ],
            [
                'step' => 2,
                'name' => 'Factory Quote',
                'description' => 'Factory provides quote per m3 or per m2',
                'duration' => '1 day',
            ],
            [
                'step' => 3,
                'name' => 'Order Acceptance',
                'description' => 'Contractor accepts quote',
                'duration' => '1 day',
            ],
            [
                'step' => 4,
                'name' => 'Processing',
                'description' => 'Factory processes wood products',
                'duration' => '3-7 days',
            ],
            [
                'step' => 5,
                'name' => 'Delivery',
                'description' => 'Wood products delivered to project site',
                'duration' => '1 day',
            ],
        ];
    }

    /**
     * Default workflow.
     */
    private function getDefaultWorkflow(): array
    {
        return [
            [
                'step' => 1,
                'name' => 'Quote Request',
                'description' => 'Contractor submits quote request',
                'duration' => '1 day',
            ],
            [
                'step' => 2,
                'name' => 'Factory Quote',
                'description' => 'Factory provides quote',
                'duration' => '1-2 days',
            ],
            [
                'step' => 3,
                'name' => 'Order Acceptance',
                'description' => 'Contractor accepts quote',
                'duration' => '1 day',
            ],
            [
                'step' => 4,
                'name' => 'Production/Preparation',
                'description' => 'Factory prepares order',
                'duration' => '3-5 days',
            ],
            [
                'step' => 5,
                'name' => 'Delivery',
                'description' => 'Products delivered to project site',
                'duration' => '1 day',
            ],
        ];
    }
}

