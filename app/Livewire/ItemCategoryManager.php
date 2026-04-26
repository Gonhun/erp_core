<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ItemCategory;
use App\Models\SubItemCategory;

class ItemCategoryManager extends Component
{
    public $iteration = 0;
    
    // Category states
    public $catIsCreating = false;
    public $catEditingId = null;
    public $new_cat_name = '';
    public $new_cat_is_active = true;
    public $edit_cat_name = '';
    public $edit_cat_is_active = true;

    // Subcategory states
    public $subIsCreating = null; // Store parent category ID
    public $subEditingId = null;
    public $new_sub_name = '';
    public $new_sub_is_active = true;
    public $edit_sub_name = '';
    public $edit_sub_is_active = true;

    public function render()
    {
        $categories = ItemCategory::with(['subCategories' => function($q) {
            $q->orderBy('sub_category_name');
        }])->orderBy('category_name')->get();

        return view('livewire.item-category-manager', [
            'categories' => $categories,
        ])->layout('layouts.app');
    }

    // --- Category Actions ---
    public function createNewCat()
    {
        $this->catIsCreating = true;
        $this->catEditingId = null;
        $this->subIsCreating = null;
        $this->subEditingId = null;
        $this->new_cat_name = '';
        $this->new_cat_is_active = true;
        $this->iteration++;
    }

    public function cancelNewCat()
    {
        $this->catIsCreating = false;
        $this->iteration++;
    }

    public function saveNewCat()
    {
        $this->validate([
            'new_cat_name' => 'required|string|max:255|unique:item_categories,category_name',
        ]);

        ItemCategory::create([
            'category_name' => $this->new_cat_name,
            'is_active' => (bool)$this->new_cat_is_active,
        ]);

        $this->catIsCreating = false;
        $this->iteration++;
    }

    public function editCat($id)
    {
        $cat = ItemCategory::findOrFail($id);
        $this->catEditingId = $id;
        $this->edit_cat_name = $cat->category_name;
        $this->edit_cat_is_active = $cat->is_active;

        $this->catIsCreating = false;
        $this->subIsCreating = null;
        $this->subEditingId = null;
        $this->iteration++;
    }

    public function cancelEditCat()
    {
        $this->catEditingId = null;
        $this->iteration++;
    }

    public function saveEditCat()
    {
        $this->validate([
            'edit_cat_name' => 'required|string|max:255|unique:item_categories,category_name,' . $this->catEditingId,
        ]);

        $cat = ItemCategory::findOrFail($this->catEditingId);
        $cat->update([
            'category_name' => $this->edit_cat_name,
            'is_active' => (bool)$this->edit_cat_is_active,
        ]);

        $this->catEditingId = null;
        $this->iteration++;
    }

    public function deleteCat($id)
    {
        ItemCategory::findOrFail($id)->delete();
        $this->iteration++;
    }

    // --- Subcategory Actions ---
    public function createNewSub($catId)
    {
        $this->subIsCreating = $catId;
        $this->subEditingId = null;
        $this->catIsCreating = false;
        $this->catEditingId = null;
        $this->new_sub_name = '';
        $this->new_sub_is_active = true;
        $this->iteration++;
    }

    public function cancelNewSub()
    {
        $this->subIsCreating = null;
        $this->iteration++;
    }

    public function saveNewSub()
    {
        $this->validate([
            'new_sub_name' => 'required|string|max:255|unique:sub_item_categories,sub_category_name',
        ]);

        SubItemCategory::create([
            'sub_category_name' => $this->new_sub_name,
            'item_category_id' => $this->subIsCreating,
            'is_active' => (bool)$this->new_sub_is_active,
        ]);

        $this->subIsCreating = null;
        $this->iteration++;
    }

    public function editSub($id)
    {
        $sub = SubItemCategory::findOrFail($id);
        $this->subEditingId = $id;
        $this->edit_sub_name = $sub->sub_category_name;
        $this->edit_sub_is_active = $sub->is_active;

        $this->subIsCreating = null;
        $this->catIsCreating = false;
        $this->catEditingId = null;
        $this->iteration++;
    }

    public function cancelEditSub()
    {
        $this->subEditingId = null;
        $this->iteration++;
    }

    public function saveEditSub()
    {
        $this->validate([
            'edit_sub_name' => 'required|string|max:255|unique:sub_item_categories,sub_category_name,' . $this->subEditingId,
        ]);

        $sub = SubItemCategory::findOrFail($this->subEditingId);
        $sub->update([
            'sub_category_name' => $this->edit_sub_name,
            'is_active' => (bool)$this->edit_sub_is_active,
        ]);

        $this->subEditingId = null;
        $this->iteration++;
    }

    public function deleteSub($id)
    {
        SubItemCategory::findOrFail($id)->delete();
        $this->iteration++;
    }
}
