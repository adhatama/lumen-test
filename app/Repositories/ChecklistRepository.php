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

    /*
    |   $data is from $request->input('data') containing data like this:
    |   [
    |       {
    |           "attributes": {
    |               "object_id": 1,
    |               "object_domain": "deals"
    |           }
    |       }
    |   ]
    */
    public function saveTemplateDomainAssignment($data, $templateChecklist, $templateItems)
    {
        DB::beginTransaction();

        $checklists = collect([]);
        foreach ($data as $val) {
            $templateChecklist['object_id'] = $val['attributes']['object_id'];
            $templateChecklist['object_domain'] = $val['attributes']['object_domain'];

            $checklist = Checklist::create($templateChecklist);

            foreach ($templateItems as $key => $item) {
                $templateItems[$key]['checklist_id'] = $checklist->id;
            }

            Item::insert($templateItems);

            $checklists->push($checklist);
        }

        DB::commit();

        return $checklists;
    }
}
