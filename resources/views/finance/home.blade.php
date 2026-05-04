@extends('layouts.app')

@section('title', 'Finance')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Finance Dashboard
    </h2>
@endsection

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 text-gray-900">
            Welcome to the Finance module.
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('finance.group-accounts') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition duration-150 ease-in-out">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Group Accounts</h5>
            <p class="font-normal text-gray-700">Manage all your finance group accounts here.</p>
        </a>

        <a href="{{ route('finance.accounts') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition duration-150 ease-in-out">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Accounts</h5>
            <p class="font-normal text-gray-700">Manage all your finance accounts and charts here.</p>
        </a>

        <a href="{{ route('finance.taxes') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition duration-150 ease-in-out">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Taxes</h5>
            <p class="font-normal text-gray-700">Manage your tax rules, computations, and scopes.</p>
        </a>

        <a href="{{ route('finance.customers') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition duration-150 ease-in-out">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Customers</h5>
            <p class="font-normal text-gray-700">Manage customer profiles, accounting settings, and tax identities.</p>
        </a>
    </div>
@endsection
