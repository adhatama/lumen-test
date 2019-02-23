<?php

namespace App\Repositories;

use App\Item;
use App\Checklist;
use Illuminate\Support\Facades\DB;
use App\Template;
use Carbon\Carbon;
use App\TemplateItem;

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

    public function saveTemplate($checklist, $items)
    {
        DB::beginTransaction();

        $template = Template::create($checklist);

        $now = Carbon::now();
        foreach ($items as $key => $item) {
            $items[$key]['template_id'] = $template->id;
            $items[$key]['created_at'] = $now;
            $items[$key]['updated_at'] = $now;
        }

        TemplateItem::insert($items);

        DB::commit();

        return $template;
    }

    public function updateTemplate(Template $template, $checklist, $items)
    {
        DB::beginTransaction();

        $template->update($checklist);

        $template->items()->delete();

        $now = Carbon::now();
        foreach ($items as $key => $item) {
            $items[$key]['template_id'] = $template->id;
            $items[$key]['created_at'] = $now;
            $items[$key]['updated_at'] = $now;
        }

        TemplateItem::insert($items);

        DB::commit();
    }
}
