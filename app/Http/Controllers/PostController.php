<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Post;
// Use App\Http\Resources\Post as PostResource;
use App\Http\Repositories\PostRepository;
use App\Http\Repositories\TagRepository;
use App\Http\Repositories\SectionRepository;
use App\Http\Repositories\ImageRepository;
use JWTAuth;
use App\Tag;
use Illuminate\Support\Str;

use App\Rules\UniqueSlug;

class PostController extends Controller
{

    protected $postRepository;
    protected $sectionRepository;
    protected $tagRepository;
    protected $nbrperPage = 5 ;

    public function __construct(PostRepository $postRepository, SectionRepository $sectionRepository, ImageRepository $imageRepository, TagRepository $tagRepository)
    {
        $this->postRepository = $postRepository;
        $this->sectionRepository = $sectionRepository;
        $this->imageRepository = $imageRepository;
        $this->tagRepository = $tagRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       /* $posts = Post::orderBy('created_at', 'desc')->paginate(5);

        return PostResource::collection($posts);*/

        $posts = $this->postRepository->getPaginate($this->nbrperPage, 'created_at', 'desc');

        // return $this->postRepository->collectionToJson($posts);

        return response()->json($posts, 200);
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
            'title' => ['required', 'max:255', new UniqueSlug($this->postRepository)],
            'body' => 'required',
            'section' => 'required',
            // 'tags' => 'required'
        ]);

        // $post = new Post();

        $title = $request->input('title');
        $slug = Str::slug($title);
        $body = $request->input('body');
        $section_id = $request->input('section');
        $section = $this->sectionRepository->getById($section_id);

         $inputs = [
                'title' => $title,
                'slug' => $slug,
                'body' => $body,
        ];

        $post = $this->postRepository->store($inputs);



        $tags_array = $request->input('tags');

        $persisted_tags = $this->tagRepository->store($tags_array)->pluck('id')->toArray();
        // $persisted_tags = Tag::whereIn('name', $tags_array)->get();

        $post->tags()->attach($persisted_tags);
        
        $post->section()->associate($section);
        


        $post->save();

        /*if($request->hasFile('images'))
        {
            $images = $request->file('images');

            $img = $this->imageRepository->moveFile($image, 'uploads/posts/');
            $input = [
                 'name' => $img,
            ];
            $persisted_img = $this->imageRepository->store($input);
            $persisted_img->post()->associate($post);
            $persisted_img->save();
        }*/

        if($post)
        {
            $post = $this->postRepository->getById($post->id);
            // return new PostResource($post);

            return $this->postRepository->objectToJson($post);
            //return response()->json(['post' => $post], 201);
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
        // $post = Post::findOrFail($id);
        $post = $this->postRepository->getById($id);
        // return new PostResource($post);

        return $this->postRepository->objectToJson($post);
    }

    public function getBySlug($slug)
    {
        $post = $this->postRepository->getBySlug($slug);
        return $this->postRepository->objectToJson($post);
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
            'title' => ['required', 'max:255', new UniqueSlug($this->postRepository, $id)],
            'body' => 'required',
            'section' => 'required',
            // 'tags' => 'required'
        ]);
        
        $post = $this->postRepository->getById($id);

        $post->title = $request->input('title');
        $post->slug = Str::slug($post->title);
        $post->body = $request->input('body');
        
        // $post->section->attach($section);
        // $post->tags->attach($tags);

        $inputs = [
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')),
            'body' => $request->input('body'),
        ];

        $post = $this->postRepository->update($id, $inputs);
        
        $tags_array = $request->input('tags');
        // $persisted_tags = Tag::whereIn('name', $tags_array)->get();
        $persisted_tags = $this->tagRepository->store($tags_array)->pluck('id')->toArray();

        $section_id = $request->input('section');
        $section = $this->sectionRepository->getById($section_id);

        $post->tags()->sync($persisted_tags);
        // $post->section()->dissociate($post->section);
        $post->section()->associate($section);

        $post->save();

        if($post)
        {
            // return new PostResource($post);
            $post = $this->postRepository->getById($post->id);

            return $this->postRepository->objectToJson($post);
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
        
        // $post = Post::findOrFail($id);

        $post = $this->postRepository->getById($id);

        foreach ($post->images as $key => $image) {
            $this->imageRepository->deleteFile($image->name, 'uploads/posts/');
            $this->imageRepository->destroy($image->id);
        }


        if($this->postRepository->destroy($id))
        {
            // return new PostResource($post);

            return $this->postRepository->objectToJson($post);
        }
    }
}
