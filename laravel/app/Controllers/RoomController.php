<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
  
    public function index() {
        return response()->json(Room::all());
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $room = Room::create($validated);
        return response()->json($room, 201);
    }

  
    public function show(Room $room) {
        return response()->json($room);
    }

   
    public function update(Request $request, Room $room) {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        $room->update($validated);
        return response()->json($room);
    }

     public function destroy(Room $room) {
        $room->delete();
        return response()->json(null, 204);
    }
}