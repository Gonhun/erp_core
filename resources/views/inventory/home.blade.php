@extends('layouts.app')

@section('title', 'Inventory')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Inventory
    </h2>
@endsection

@section('content')
    <style>
        .menu-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
            text-decoration: none !important;
            height: 100%;
        }

        .menu-card:hover {
            border-color: #008784;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .menu-icon {
            width: 40px;
            height: 40px;
            background: #f3f4f6;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            color: #008784;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .menu-card:hover .menu-icon {
            background: #008784;
            color: white;
        }

        .menu-content {
            flex-grow: 1;
        }

        .menu-title {
            font-weight: 700;
            color: #111827;
            margin-bottom: 2px;
            font-size: 0.95rem;
        }

        .menu-desc {
            font-size: 0.75rem;
            color: #6b7280;
            line-height: 1.2;
        }
    </style>

    <div class="mb-4">
        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Warehouse & Logistics</h4>
        <div class="row g-3">
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('inventory.warehouses') }}" class="menu-card">
                    <div class="menu-icon"><i class="mdi mdi-warehouse"></i></div>
                    <div class="menu-content">
                        <div class="menu-title">Warehouses</div>
                        <div class="menu-desc">Main storage structures</div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('inventory.warehouse-locations') }}" class="menu-card">
                    <div class="menu-icon"><i class="mdi mdi-map-marker-path"></i></div>
                    <div class="menu-content">
                        <div class="menu-title">Locations</div>
                        <div class="menu-desc">Bins, shelves & aisles</div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('inventory.location-categories') }}" class="menu-card">
                    <div class="menu-icon"><i class="mdi mdi-layers-outline"></i></div>
                    <div class="menu-content">
                        <div class="menu-title">Categories</div>
                        <div class="menu-desc">Location types & types</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Products & Catalog</h4>
        <div class="row g-3">
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('inventory.products') }}" class="menu-card">
                    <div class="menu-icon"><i class="mdi mdi-package-variant"></i></div>
                    <div class="menu-content">
                        <div class="menu-title">Products</div>
                        <div class="menu-desc">Full product catalog</div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('inventory.item-categories') }}" class="menu-card">
                    <div class="menu-icon"><i class="mdi mdi-shape-outline"></i></div>
                    <div class="menu-content">
                        <div class="menu-title">Item Categories</div>
                        <div class="menu-desc">Hierarchical grouping</div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('inventory.brands') }}" class="menu-card">
                    <div class="menu-icon"><i class="mdi mdi-tag-outline"></i></div>
                    <div class="menu-content">
                        <div class="menu-title">Brands</div>
                        <div class="menu-desc">Manufacturer management</div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('inventory.product-types') }}" class="menu-card">
                    <div class="menu-icon"><i class="mdi mdi-cube-send"></i></div>
                    <div class="menu-content">
                        <div class="menu-title">Product Types</div>
                        <div class="menu-desc">Storable, Service, etc.</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Units & Measures</h4>
        <div class="row g-3">
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('inventory.uom-categories') }}" class="menu-card">
                    <div class="menu-icon"><i class="mdi mdi-scale-balance"></i></div>
                    <div class="menu-content">
                        <div class="menu-title">UoM Categories</div>
                        <div class="menu-desc">Weight, Volume, Units</div>
                    </div>
                </a>
            </div>
            <!-- <div class="col-md-4 col-lg-3">
                    <a href="{{ route('inventory.uoms') }}" class="menu-card">
                        <div class="menu-icon"><i class="mdi mdi-ruler"></i></div>
                        <div class="menu-content">
                            <div class="menu-title">Units of Measure</div>
                            <div class="menu-desc">Specific conversion units</div>
                        </div>
                    </a>
                </div> -->
        </div>
    </div>
@endsection