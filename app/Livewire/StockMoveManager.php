<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\StockMove;

class StockMoveManager extends Component
{
    public function render()
    {
        $moves = StockMove::with(['product', 'warehouse'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.stock-move-manager', [
            'moves' => $moves
        ])->layout('layouts.app');
    }
}
