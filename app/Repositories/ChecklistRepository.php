<?php

namespace App\Repositories;

use App\Item;
use App\Checklist;

class ChecklistRepository
{
    public function getItemsByIds($checklistId, $itemIds)
    {
        return Item::where('checklist_id', $checklistId)
            ->whereIn('id', $itemIds)->get();
    }

    public function completeItems($checklistId, $itemIds)
    {
        return Item::where('checklist_id', $checklistId)
            ->whereIn('id', $itemIds)->update(['is_completed' => true]);
    }

    public function uncompleteItems($checklistId, $itemIds)
    {
        return Item::where('checklist_id', $checklistId)
            ->whereIn('id', $itemIds)->update(['is_completed' => false]);
    }

    public function countIncompleteItems($checklistId)
    {
        return Item::where('checklist_id', $checklistId)
            ->where('is_completed', false)
            ->count();
    }

    public function completeChecklist($checklistId)
    {
        return Checklist::where('id', $checklistId)
                ->update(['is_completed' => true]);
    }

    public function uncompleteChecklist($checklistId)
    {
        return Checklist::where('id', $checklistId)
                ->update(['is_completed' => false]);
    }
}
