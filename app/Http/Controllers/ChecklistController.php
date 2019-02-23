<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Checklist as ChecklistResource;
use App\Http\Resources\Item as ItemResource;
use App\Checklist;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\JsonResponse;
use App\Item;

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

        Item::create($attributes);

        return new ChecklistResource($checklist, true);
    }
}
