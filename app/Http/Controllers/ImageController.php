<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\ImageRepository;
use App\Http\Repositories\PostRepository;

class ImageController extends Controller
{

    protected $imageRepository;
    protected $postRepository;

    public function __construct(ImageRepository $imageRepository, PostRepository $postRepository)
    {
        $this->imageRepository = $imageRepository;
        $this->postRepository = $postRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //

        $this->validate($request, [
            'images' => 'required',
            'post' => 'required'
        ]);

        // if($request->hasFile('images'))

        $post_id = $request->input('post');
        $post = $this->postRepository->getById($post_id);

        $persisted_imgs = [];
        if($request->hasFile('images'))
        {
            $images = $request->file('images');
            foreach($images as $image)
            {


            $img = $this->imageRepository->moveFile($image, 'uploads/posts/');
            $input = [
                 'name' => $img,
            ];
            $persisted_img = $this->imageRepository->store($input);
            $persisted_img->post()->associate($post);
            $persisted_img->save();
            $persisted_imgs[] = $persisted_img;
            }
        }
        return response()->json(['images' => $persisted_imgs], 201);
        // return $this->imageRepository->collectionToJson($persisted_imgs);

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
            'image' => 'required|image',
        ]);

        $image = $this->imageRepository->getById($id);

        if(!$image)
        {
            return response()->json(['message' => 'Image not found.'], 404);
        }

        if($request->hasFile('image'))
        {
            $new_image = $request->file('image');

            $this->imageRepository->deleteFile($image->name, 'uploads/posts/');
            $img = $this->imageRepository->moveFile($new_image, /*storage_path('app/posts')*/ 'uploads/posts/');
        }
        else
        {
            $img = $image->name;
        }

        $inputs = [
                'name' => $img
            ];

        $image = $this->imageRepository->update($id, $inputs);

        $response = [
            'image' => $image
        ];

        return response()->json($response, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $image = $this->imageRepository->getById($id);

        if(!$image)
        {
            return response()->json(['message' => 'Image not found.'], 404);
        }
        
        $this->imageRepository->deleteFile($image->name, 'uploads/posts/');
        $image = $this->imageRepository->destroy($id);

        $response = [
            'message' => 'Image deleted'
        ];

        return response()->json($response, 200);
    }
}
