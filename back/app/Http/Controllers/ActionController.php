<?php

namespace App\Http\Controllers;

use App\Models\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActionController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'date_init' => 'required|date',
            'date_end' => 'required|date',
            'age' => 'required|integer',
            'languaje' => 'required|string|max:255',
            'duration' => 'required|integer',
            'start_time' => 'required|date',
            'capacity' => 'required|integer',
            'price' => 'required|integer',
            'center_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $action = Action::create([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'date_init' => $request->get('date_init'),
            'date_end' => $request->get('date_end'),
            'age' => $request->get('age'),
            'languaje' => $request->get('languaje'),
            'duration' => $request->get('duration'),
            'start_time' => $request->get('start_time'),
            'capacity' => $request->get('capacity'),
            'price' => $request->get('price'),
            'center_id' => $request->get('center_id')
        ]);
        return response()->json(['message' => 'Accion creada', 'data' => $action], 200);
    }
    public function showAll()
    {
        $actions = Action::with('center')->get();
        return response()->json(['message' => '', 'data' => $actions], 200);
    }

    public function show($id)
    {
        $action = Action::findOrFail($id);
        return response()->json(['message' => '', 'data' => $action], 200);
    }
    public function update(Request $request, $id)
    {
        $action = Action::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:255',
            'date_init' => 'sometimes|date',
            'date_end' => 'sometimes|date',
            'age' => 'sometimes|integer',
            'duration' => 'sometimes|integer',
            'languaje' => 'sometimes|string|max:255',
            'start_time' => 'sometimes|date',
            'capacity' => 'sometimes|integer',
            'price' => 'sometimes|integer',
            'center_id' => 'sometimes|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $action->name = $request->get('name', $action->name);
        $action->description = $request->get('description', $action->description);
        $action->date_init = $request->get('date_init', $action->date_init);
        $action->date_end = $request->get('date_end', $action->date_end);
        $action->age = $request->get('age', $action->age);
        $action->duration = $request->get('duration', $action->duration);
        $action->languaje = $request->get('languaje', $action->languaje);
        $action->start_time = $request->get('start_time', $action->start_time);
        $action->capacity = $request->get('capacity', $action->capacity);
        $action->price = $request->get('price', $action->price);
        $action->center_id = $request->get('center_id', $action->center_id);
        $action->save();
        return response()->json(['message' => 'Acción actualizada', 'data' => $action], 200);
    }
    public function destroy($id)
    {
        $action = Action::findOrFail($id);
        return response()->json(['message'=>'Accion eliminada', 'data' => $action], 200);
    }

    public function center()
    {
        $actions = Action::select(
            'actions.*',
            'centers.name as center_name',
            'centers.address as center_address'
        )
            ->join('centers', 'actions.center_id', '=', 'centers.id')
            ->get();

        return response()->json(['message' => '', 'data' => $actions], 200);
    }
}
