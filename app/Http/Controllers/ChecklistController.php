<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Checklist as ChecklistResource;
use App\Checklist;

class ChecklistController extends Controller
{
    public function index(Request $request)
    {
        $checklists = Checklist::all();

        return ChecklistResource::collection($checklists);
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'data.attributes.object_domain' => 'required',
            'data.attributes.object_id' => 'required',
            'data.attributes.description' => 'required',
        ]);

        $checklist = $request->input('data.attributes');

        $checklist = Checklist::create($checklist);

        return new ChecklistResource($checklist);
    }
}
