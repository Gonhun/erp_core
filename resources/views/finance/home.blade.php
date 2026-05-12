@extends('layouts.app')

@section('title', 'Finance')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Finance
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
        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Master Data</h4>
        <div class="row g-3">
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('finance.customers') }}" class="menu-card">
                    <div class="menu-icon">
                        <i class="mdi mdi-account-group"></i>
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">Customers</div>
                        <div class="menu-desc">Profiles, accounting & tax IDs</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Accounting</h4>
        <div class="row g-3">
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('finance.group-accounts') }}" class="menu-card">
                    <div class="menu-icon">
                        <i class="mdi mdi-folder-account"></i>
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">Group Accounts</div>
                        <div class="menu-desc">COA categories & hierarchy</div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('finance.accounts') }}" class="menu-card">
                    <div class="menu-icon">
                        <i class="mdi mdi-book-account"></i>
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">Accounts</div>
                        <div class="menu-desc">Detailed chart of accounts</div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-lg-3">
                <a href="{{ route('finance.taxes') }}" class="menu-card">
                    <div class="menu-icon">
                        <i class="mdi mdi-percent"></i>
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">Taxes</div>
                        <div class="menu-desc">Rates, PPN & tax rules</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
