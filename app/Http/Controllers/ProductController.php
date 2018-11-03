<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\ProductRepository;
use App\Product;

class ProductController extends Controller
{

    protected $productRepository;
    protected $nbrperPage = 5 ;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       /* $products = Product::orderBy('created_at', 'desc')->paginate(5);

        return ProductResource::collection($products);*/

        $products = $this->productRepository->getPaginate($this->nbrperPage, 'created_at', 'desc');

        return $this->productRepository->collectionToJson($products);
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
            'description' => 'required|max:255',
            'price' => 'required|numeric',
        ]);

        // $product = new Product();

        // $product->name = $request->input('name');
        // $product->body = $request->input('body');

        $name = $request->input('name');
        $description = $request->input('description');
        $price = $request->input('price');

        $inputs = [
                'name' => $name,
                'description' => $description,
                'price' => $price,
        ];

        $product = $this->productRepository->store($inputs);

        // $product->product->attach($product);
        // $product->tags->attach($tags);

        if($product)
        {
            // return new ProductResource($product);

            return $this->productRepository->objectToJson($product);
            // return response()->json(['product' => $product, 'user' => $user], 201);
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
        // $product = Product::findOrFail($id);
        $product = $this->productRepository->getById($id);
        // return new ProductResource($product);

        return $this->productRepository->objectToJson($product);
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
        $name = $request->input('name');
        $description = $request->input('description');
        $price = $request->input('price');
        
        // $product->product->attach($product);
        // $product->tags->attach($tags);

        $inputs = [
            'name' => $name,
            'description' => $description,
            'price' => $price,
        ];

        $product = $this->productRepository->update($id, $inputs);

        if($product)
        {
            // return new ProductResource($product);

            return $this->productRepository->objectToJson($product);
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
        
        // $product = Product::findOrFail($id);

        $product = $this->productRepository->getById($id);


        if($this->productRepository->destroy($id))
        {
            // return new ProductResource($product);

            return $this->productRepository->objectToJson($product);
        }
    }
}