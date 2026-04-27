<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Event::where('user_id', auth()->id());

        $query->when(
            $request->filled('start') && $request->filled('end'),
            fn($q) => $q->where('start_at', '>=', $request->start)
                        ->where('start_at', '<=', $request->end)
        );

        return EventResource::collection($query->orderBy('start_at')->get());
    }

    public function store(StoreEventRequest $request): EventResource
    {
        $event = Event::create([...$request->validated(), 'user_id' => auth()->id()]);

        return new EventResource($event);
    }

    public function show(Event $event): EventResource
    {
        $this->authorize('view', $event);

        return new EventResource($event);
    }

    public function update(UpdateEventRequest $request, Event $event): EventResource
    {
        $this->authorize('update', $event);

        $event->update($request->validated());

        return new EventResource($event);
    }

    public function destroy(Event $event): JsonResponse
    {
        $this->authorize('delete', $event);

        $event->delete();

        return response()->json(null, 204);
    }
}
