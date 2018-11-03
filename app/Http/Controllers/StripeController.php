<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\ProductRepository;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;

class StripeController extends Controller
{

    private $productRepository;
    private $total_price;


    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
        Stripe::setApiKey(env('STRIPE_SECRET'));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function checkout(Request $request)
    {
        $items = json_decode($request->items, true);
        foreach ($items as $_item) {
 
            $product_id = $_item['id'];
            $product = $this->productRepository->getById($product_id);

            $product_quantity = $_item['quantity'];
            $product_name = $product->name;
            $product_price = $product->price;

            $this->total_price += ($product_price * $product_quantity);
        }
 
        $token = $request->stripeToken;
        
        // $customer = Customer::create(array(
        //     'email' => 'belahcel.yanis@gmail.com',
        //     'source'  => $request->stripeToken,
        // ));

        $charge = Charge::create([
            'amount' => $this->total_price*100,
            'currency' => 'usd',
            'source' => $token,
            // 'customer' => $customer->id,
            // 'receipt_email' => 'belahcel.yanis@gmail.com',
        ]);

        return response()->json($request->all(), 200);
    }
}
