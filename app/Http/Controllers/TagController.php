<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\TagRepository;
use Illuminate\Support\Str;
use App\Tag;
use App\Rules\UniqueSlug;

class TagController extends Controller
{

    protected $tagRepository;
    protected $nbrperPage = 5 ;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = $this->tagRepository->getPaginate($this->nbrperPage, 'created_at', 'desc');

        return $this->tagRepository->collectionToJson($tags);
    }

    public function search($query)
    {
        $tags = $this->tagRepository->search($query, 12);

        return $this->tagRepository->collectionToJson($tags);
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
            'tags' => ['required', new UniqueSlug($this->tagRepository)],
        ]);

        $tags_array = $request->input('tags');

        // $persisted_tags = $this->tagRepository->persisted('name', $tags_array);
        // $tags = explode(',', $tags);
         
         // $persisted_tags = Tag::whereIn('name', $tags_array)->get();

         // $tags_array = array_diff($tags_array, $persisted_tags->pluck('name')->all());

        /*foreach ($tags_array as $key => $tag) {
            $inputs = [
                'name' => $tag["tag"],
                'slug' => Str::slug($tag["tag"])
            ];
             $this->tagRepository->store($inputs);
        }*/
        // $tags_array = array_map(function($tag){
        //     return ['name' => $tag, 'slug' => Str::slug($tag)];
        // }, $tags_array);

        // $tags = collect([]);
        //$tags = $this->tagRepository->store($tags_array);

        // foreach ($tags_array as $key => $tag) {
        //     $tags->push($this->tagRepository->store($tag));
        // }
        // $tags = $this->tagRepository->insert($tags_array);

        $tags = $this->tagRepository->store($tags_array);

        if($tags)
        {
           return $this->tagRepository->collectionToJson($tags);
            // return response()->json(['tags' => $persisted_tags], 201);
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
        $tag = $this->tagRepository->getById($id);

        return $this->tagRepository->objectToJson($tag);
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
            'name' => ['required', new UniqueSlug($this->tagRepository, $id)],
        ]);

        $tag = $this->tagRepository->getById($id);

        $tag_name =  $request->input('name');

        $inputs = [
            'name' => $tag_name,
            'slug' => Str::slug($tag_name),
        ];

        $tag = $this->tagRepository->update($id, $inputs);

        if($tag)
        {
            return $this->tagRepository->objectToJson($tag);
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
        
        $tag = $this->tagRepository->getById($id);


        if($this->tagRepository->destroy($id))
        {
            return $this->tagRepository->objectToJson($tag);
        }
    }
}
