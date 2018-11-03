<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\SectionRepository;
use Illuminate\Support\Str;
use App\Section;

class SectionController extends Controller
{

    protected $sectionRepository;
    protected $nbrperPage = 5 ;

    public function __construct(SectionRepository $sectionRepository)
    {
        $this->sectionRepository = $sectionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       /* $sections = Section::orderBy('created_at', 'desc')->paginate(5);

        return SectionResource::collection($sections);*/

        $sections = $this->sectionRepository->getPaginate($this->nbrperPage, 'created_at', 'desc');

        return $this->sectionRepository->collectionToJson($sections);
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
        // $user = JWTAuth::parseToken()->toUser(); // Get the user (Make sure that the token is sent)

        $this->validate($request, [
            'name' => 'required|max:255',
        ]);

        // $section = new Section();

        // $section->name = $request->input('name');
        // $section->body = $request->input('body');

        $name = $request->input('name');
        $slug = Str::slug($name);

        $inputs = [
                'name' => $name,
                'slug' => $slug
        ];

        $section = $this->sectionRepository->store($inputs);

        // $section->section->attach($section);
        // $section->tags->attach($tags);

        if($section)
        {
            // return new SectionResource($section);

            return $this->sectionRepository->objectToJson($section);
            // return response()->json(['section' => $section, 'user' => $user], 201);
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
        // $section = Section::findOrFail($id);
        $section = $this->sectionRepository->getById($id);
        // return new SectionResource($section);

        return $this->sectionRepository->objectToJson($section);
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
        $section = $this->sectionRepository->getById($id);

        $section->name = $request->input('name');
        
        // $section->section->attach($section);
        // $section->tags->attach($tags);

        $inputs = [
            'name' => $request->input('name'),
        ];

        $section = $this->sectionRepository->update($id, $inputs);

        if($section)
        {
            // return new SectionResource($section);

            return $this->sectionRepository->objectToJson($section);
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
        
        // $section = Section::findOrFail($id);

        $section = $this->sectionRepository->getById($id);


        if($this->sectionRepository->destroy($id))
        {
            // return new SectionResource($section);

            return $this->sectionRepository->objectToJson($section);
        }
    }
}
