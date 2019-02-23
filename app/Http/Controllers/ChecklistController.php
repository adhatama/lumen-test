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
use Carbon\Carbon;
use App\Repositories\ChecklistRepository;

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

    public function completeItems(Request $request, ChecklistRepository $checklistRepo, $checklistId)
    {
        $checklist = Checklist::find($checklistId);

        if (!$checklist) {
            throw new NotFoundHttpException();
        }

        $itemIds = collect($request->input('data'))->pluck('item_id');

        $checklistRepo->completeItems($checklistId, $itemIds);

        $countIncompleteItems = $checklistRepo->countIncompleteItems($checklistId);
        if ($checklist->hasItems() && $countIncompleteItems === 0) {
            $checklistRepo->completeChecklist($checklistId);
        }

        $items = $checklistRepo->getItemsByIds($checklistId, $itemIds);

        $data = [];
        foreach ($items as $item) {
            $val['item_id'] = $item->id;
            $val['is_completed'] = $item->is_completed;
            $val['checklist_id'] = $item->checklist->id;

            array_push($data, $val);
        }

        return response()->json(['data' => $data], JsonResponse::HTTP_OK);
    }

    public function incompleteItems(Request $request, ChecklistRepository $checklistRepo, $checklistId)
    {
        $checklist = Checklist::find($checklistId);

        if (!$checklist) {
            throw new NotFoundHttpException();
        }

        $itemIds = collect($request->input('data'))->pluck('item_id');

        $checklistRepo->uncompleteItems($checklistId, $itemIds);

        $countIncompleteItems = $checklistRepo->countIncompleteItems($checklistId);
        if ($checklist->hasItems() && $countIncompleteItems > 0) {
            $checklistRepo->uncompleteChecklist($checklistId);
        }

        $items = $checklistRepo->getItemsByIds($checklistId, $itemIds);

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

    public function saveTemplate(Request $request, ChecklistRepository $checklistRepo)
    {
        $this->validate($request, [
            'data.attributes.name' => 'required',
            'data.attributes.checklist.description' => 'required',
            'data.attributes.items' => 'required',
            'data.attributes.items.*.description' => 'required',
        ]);

        $attributes = $request->input('data.attributes');

        $checklist = $attributes['checklist'];
        $checklist['name'] = $attributes['name'];

        $items = $attributes['items'];

        $template = $checklistRepo->saveTemplate($checklist, $items);

        return new TemplateResource($template);
    }

    public function updateTemplate(Request $request, ChecklistRepository $checklistRepo, $id)
    {
        $attributes = $request->input('data.attributes');

        $checklist = $attributes['checklist'];
        $checklist['name'] = $attributes['name'];

        $items = $attributes['items'];

        $template = Template::find($id);

        if (!$template) {
            throw new NotFoundHttpException();
        }

        $checklistRepo->updateTemplate($template, $checklist, $items);

        return new TemplateResource($template);
    }

    public function deleteTemplate(Request $request, $id)
    {
        $template = Template::find($id);

        if (!$template) {
            throw new NotFoundHttpException();
        }

        $template->delete();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    public function assignTemplate(Request $request, ChecklistRepository $checklistRepo, $id)
    {
        $this->validate($request, [
            'data.*.attributes.object_id' => 'required',
            'data.*.attributes.object_domain' => 'required',
        ]);

        $template = Template::find($id);

        if (!$template) {
            throw new NotFoundHttpException();
        }

        $now = Carbon::now();

        $templateChecklist = [
            'description' => $template->description,
        ];

        $due = $template->getDueInDate();
        if ($due) {
            $templateChecklist['due'] = $due;
        }

        $templateItems = [];
        foreach ($template->items as $item) {
            $val['description'] = $item->description;
            $val['urgency'] = $item->urgency;
            $val['due'] = $item->getDueInDate();
            $val['is_completed'] = false;
            $val['created_at'] = $now;
            $val['updated_at'] = $now;

            array_push($templateItems, $val);
        }

        $checklists = $checklistRepo->saveTemplateDomainAssignment(
            $request->input('data'),
            $templateChecklist,
            $templateItems
        );

        return ChecklistResource::collection($checklists);
    }
}
