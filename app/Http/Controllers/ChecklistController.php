<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Checklist as ChecklistResource;
use App\Http\Resources\Item as ItemResource;
use App\Http\Resources\Template as TemplateResource;
use App\Checklist;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\JsonResponse;
use App\Item;
use App\Template;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\TemplateItem;

class ChecklistController extends Controller
{
    public function index(Request $request)
    {
        $checklists = Checklist::paginate(config('app.pagination.per_page'));

        return ChecklistResource::collection($checklists);
    }

    public function get(Request $request, $id)
    {
        $checklist = Checklist::find($id);

        if (!$checklist) {
            throw new NotFoundHttpException();
        }

        return new ChecklistResource($checklist);
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'data.attributes.object_domain' => 'required',
            'data.attributes.object_id' => 'required',
            'data.attributes.description' => 'required',
        ]);

        $attributes = $request->input('data.attributes');

        $checklist = Checklist::create($attributes);

        return new ChecklistResource($checklist);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'data.type' => 'required',
            'data.id' => 'required',
        ]);

        $checklist = Checklist::find($id);

        if (!$checklist) {
            throw new NotFoundHttpException();
        }

        $attributes = $request->input('data.attributes');

        $checklist->update($attributes);

        return new ChecklistResource($checklist);
    }

    public function delete($id)
    {
        $checklist = Checklist::find($id);

        if (!$checklist) {
            throw new NotFoundHttpException();
        }

        $checklist->delete();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    public function indexItem(Request $request, $checklistId)
    {
        $checklist = Checklist::find($checklistId);

        if (!$checklist) {
            throw new NotFoundHttpException();
        }

        return new ChecklistResource($checklist, true);
    }

    public function getItem(Request $request, $checklistId, $itemId)
    {
        $checklist = Checklist::find($checklistId);

        if (!$checklist) {
            throw new NotFoundHttpException();
        }

        $item = Item::find($itemId);

        if (!$item) {
            throw new NotFoundHttpException();
        }

        return new ItemResource($item);
    }

    public function saveItem(Request $request, $checklistId)
    {
        $this->validate($request, [
            'data.attributes.description' => 'required',
        ]);

        $checklist = Checklist::find($checklistId);

        if (!$checklist) {
            throw new NotFoundHttpException();
        }

        $attributes = $request->input('data.attributes');
        $attributes['checklist_id'] = $checklistId;

        $item = Item::create($attributes);

        return new ItemResource($item);
    }

    public function updateItem(Request $request, $checklistId, $itemId)
    {
        $checklist = Checklist::find($checklistId);

        if (!$checklist) {
            throw new NotFoundHttpException();
        }

        $item = Item::find($itemId);

        if (!$item) {
            throw new NotFoundHttpException();
        }

        $attributes = $request->input('data.attributes');
        $attributes['checklist_id'] = $checklistId;

        $item->update($attributes);

        return new ItemResource($item);
    }

    public function deleteItem(Request $request, $checklistId, $itemId)
    {
        $checklist = Checklist::find($checklistId);

        if (!$checklist) {
            throw new NotFoundHttpException();
        }

        $item = Item::find($itemId);

        if (!$item) {
            throw new NotFoundHttpException();
        }

        $item->delete();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    public function completeItems(Request $request, $checklistId)
    {
        $checklist = Checklist::find($checklistId);

        if (!$checklist) {
            throw new NotFoundHttpException();
        }

        $itemIds = collect($request->input('data'))->pluck('item_id');

        Item::whereIn('id', $itemIds)->update(['is_completed' => true]);

        $countIncompleteItems = Item::where('checklist_id', $checklistId)
            ->where('is_completed', false)
            ->count();
        if ($checklist->items()->count() > 0 && $countIncompleteItems === 0) {
            Checklist::where('id', $checklistId)
                ->update(['is_completed' => true]);
        }

        $items = Item::whereIn('id', $itemIds)->get();

        $data = [];
        foreach ($items as $item) {
            $val['item_id'] = $item->id;
            $val['is_completed'] = $item->is_completed;
            $val['checklist_id'] = $item->checklist->id;

            array_push($data, $val);
        }

        return response()->json(['data' => $data], JsonResponse::HTTP_OK);
    }

    public function incompleteItems(Request $request, $checklistId)
    {
        $checklist = Checklist::find($checklistId);

        if (!$checklist) {
            throw new NotFoundHttpException();
        }

        $itemIds = collect($request->input('data'))->pluck('item_id');

        Item::whereIn('id', $itemIds)->update(['is_completed' => false]);

        $countIncompleteItems = Item::where('checklist_id', $checklistId)
            ->where('is_completed', false)
            ->count();
        if ($checklist->items()->count() > 0 && $countIncompleteItems > 0) {
            Checklist::where('id', $checklistId)
                ->update(['is_completed' => false]);
        }

        $items = Item::whereIn('id', $itemIds)->get();

        $data = [];
        foreach ($items as $item) {
            $val['item_id'] = $item->id;
            $val['is_completed'] = $item->is_completed;
            $val['checklist_id'] = $item->checklist->id;

            array_push($data, $val);
        }

        return response()->json(['data' => $data], JsonResponse::HTTP_OK);
    }

    public function indexTemplate(Request $request)
    {
        $templates = Template::paginate(config('app.pagination.per_page'));

        return TemplateResource::collection($templates);
    }

    public function getTemplate(Request $request, $id)
    {
        $template = Template::find($id);

        if (!$template) {
            throw new NotFoundHttpException();
        }

        return new TemplateResource($template);
    }

    public function saveTemplate(Request $request)
    {
        $this->validate($request, [
            'data.attributes.name' => 'required',
            'data.attributes.checklist.description' => 'required',
            'data.attributes.items.*.description' => 'required',
        ]);

        $attributes = $request->input('data.attributes');

        $checklist = $attributes['checklist'];
        $checklist['name'] = $attributes['name'];

        $items = $attributes['items'];

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

        return new TemplateResource($template);
    }

    public function updateTemplate(Request $request, $id)
    {
        $attributes = $request->input('data.attributes');

        $checklist = $attributes['checklist'];
        $checklist['name'] = $attributes['name'];

        $items = $attributes['items'];

        $template = Template::find($id);

        if (!$template) {
            throw new NotFoundHttpException();
        }

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

        return new TemplateResource($template);
    }

    public function deleteTemplate(Request $request, $id)
    {
        $template = Template::find($id);

        if (!$template) {
            throw new NotFoundHttpException();
        }

        $template->delete();

        return new TemplateResource($template);
    }
}
