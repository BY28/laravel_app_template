<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Repositories\EventRepository;
use Illuminate\Support\Carbon;

class EventController extends Controller
{
    protected $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = $this->eventRepository->get();

        return response()->json(['data' => $events], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'start' => 'required|date',
            'end' => 'required|date',
            'description' => 'required|max:1000',
        ]);

        $title = $request->input('title');

        $start = new Carbon($request->input('start'));
        $start = $start->format('Y-m-d H:i:s');

        $end = new Carbon($request->input('end'));
        $end = $end->format('Y-m-d H:i:s');

        $description = $request->input('description');

        $inputs = [
            'title' => $title,
            'start' => $start,
            'end' => $end,
            'description' => $description,
        ];

        $event = $this->eventRepository->store($inputs);

        if($event)
        {
            return response()->json(['data' => $event], 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'start' => 'required|date',
            'end' => 'required|date',
            'description' => 'required|max:1000',
        ]);

        $title = $request->input('title');
        
        $start = new Carbon($request->input('start'));
        $start = $start->format('Y-m-d H:i:s');

        $end = new Carbon($request->input('end'));
        $end = $end->format('Y-m-d H:i:s');

        $description = $request->input('description');

        $inputs = [
            'title' => $title,
            'start' => $start,
            'end' => $end,
            'description' => $description,
        ];

        $event = $this->eventRepository->update($id, $inputs);

        if($event)
        {
            return response()->json(['data' => $event], 201);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($event = $this->eventRepository->destroy($id))
        {
            return response()->json(['data' => $event], 201);
        }
    }
}
