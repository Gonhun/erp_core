@extends('layouts.app')

@section('title', 'Inventory')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Inventory Dashboard
    </h2>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 text-gray-900">
            Welcome to the Inventory module.
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('inventory.location-categories') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition duration-150 ease-in-out">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Location Categories</h5>
            <p class="font-normal text-gray-700">Manage categories and types for your warehouse bins and locations.</p>
        </a>

        <a href="{{ route('inventory.warehouses') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition duration-150 ease-in-out">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Warehouses</h5>
            <p class="font-normal text-gray-700">Manage your main warehouse structures and logistics.</p>
        </a>

        <a href="{{ route('inventory.warehouse-locations') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition duration-150 ease-in-out">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Warehouse Locations</h5>
            <p class="font-normal text-gray-700">Manage your detailed bins, shelves, and routing locations.</p>
        </a>

        <a href="{{ route('inventory.uom-categories') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition duration-150 ease-in-out">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">UoM Categories</h5>
            <p class="font-normal text-gray-700">Define Unit of Measure categories and their base configurations.</p>
        </a>

        <a href="{{ route('inventory.brands') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition duration-150 ease-in-out">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Brands</h5>
            <p class="font-normal text-gray-700">Manage product brands and their visibility status.</p>
        </a>

        <a href="{{ route('inventory.item-categories') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition duration-150 ease-in-out">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Item Categories</h5>
            <p class="font-normal text-gray-700">Organize products into hierarchical categories and sub-categories.</p>
        </a>

        <a href="{{ route('inventory.product-types') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition duration-150 ease-in-out">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Product Types</h5>
            <p class="font-normal text-gray-700">Define different types of products (e.g., Storable, Service, Consumable).</p>
        </a>

        <a href="{{ route('inventory.uoms') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition duration-150 ease-in-out">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Units of Measure</h5>
            <p class="font-normal text-gray-700">Manage specific units of measure and their conversion ratios.</p>
        </a>

        <a href="{{ route('inventory.products') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition duration-150 ease-in-out">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Products</h5>
            <p class="font-normal text-gray-700">Manage your product catalog, specifications, and configurations.</p>
        </a>
    </div>
@endsection
