@extends('layouts.app')

@section('title', 'Purchasing')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Purchasing Dashboard
    </h2>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
        <div class="p-6 text-gray-900">
            Welcome to the Purchasing module. Manage your vendor relations and procurement cycle here.
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('purchasing.suppliers') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition duration-150 ease-in-out">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Suppliers</h5>
            <p class="font-normal text-gray-700">Manage vendor profiles, accounting links, and bank details.</p>
        </a>

        <a href="{{ route('purchasing.orders') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition duration-150 ease-in-out">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Purchase Orders</h5>
            <p class="font-normal text-gray-700">Manage Quotations, RFQs, and POs.</p>
        </a>
    </div>
@endsection
