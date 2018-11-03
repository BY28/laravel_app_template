<?php

namespace App\Http\Controllers;

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\PaymentExecution;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

use App\Http\Repositories\ProductRepository;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class PaypalController extends Controller
{
    private $_api_context;
    private $productRepository;
    private $total_price;

    public function __construct(ProductRepository $productRepository)
    {
        $paypal_conf = \Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
            $paypal_conf['client_id'],
            $paypal_conf['secret']
        ));
        $this->_api_context->setConfig($paypal_conf['settings']);

        $this->productRepository = $productRepository;
    }

    public function checkout(Request $request)
    {
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

       /* $item1 = new Item();
        $item1->setName($request->input('name'))
            ->setCurrency('USD')
            ->setQuantity($request->input('quantity'))
            // ->setSku("123123") // Similar to `item_number` in Classic API
            ->setPrice($request->input('amount'));*/
        
        $itemList = new ItemList();
        // $itemList->setItems(array($item1/*, $item2*/));

        $items = json_decode($request->items, true);
        // $items = [["name" => 'item', "price" => 19, "quantity" => 1]];
        // var_dump($items);
        // $total_price = $request->total_price;

        foreach ($items as $_item) {
 
            $product_id = $_item['id'];
            $product = $this->productRepository->getById($product_id);

            $product_quantity = $_item['quantity'];
            $product_name = $product->name;
            $product_price = $product->price;

            $this->total_price += ($product_price * $product_quantity);


            $item = new Item();
            $item->setName($product_name)
                ->setCurrency('USD')
                ->setQuantity($product_quantity)
                ->setPrice($product_price);

            $itemList->addItem($item);
        }

        $details = new Details();
        $details->setSubtotal($this->total_price);
        //     ->setTax(1.3)
        //     ->setShipping(1.2);

        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal($this->total_price)
            ->setDetails($details);

        $transaction = new Transaction();
        $transaction->setItemList($itemList)
            ->setAmount($amount)
            ->setDescription("Payment description");
            // ->setInvoiceNumber(uniqid());

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(URL::to('cart'))
            ->setCancelUrl(URL::to('cart'));

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions([$transaction]);

        try {
            
            $payment->create($this->_api_context);
            return response()->json(['id' => $payment->getId()], 200);
            // echo json_encode([
            //     'id' => $payment->getId(),
            // ]);
            // var_dump($payment);
        } catch (\Paypal\Exception\PPConnectionEexception $e) {
            
            var_dump(json_decode($e->getData()));

            // if(\Config::get('app.debug'))
            // {
            //     // Connection timeout
            // }
            // else
            // {
            //     // The transaction couldn't be completed, please check your connection and try again.
            // }

        }
        // echo $payment->getApprovalLink();
        // $payment_id = $payment->getId();

    }

    public function execute(Request $request)
    {
        $payment = Payment::get($request->input('paymentID'), $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId($request->input('payerID'))
            ->setTransactions($payment->getTransactions());

        try {

            $payment->execute($execution, $this->_api_context);
            echo json_encode([
                'id' => $payment->getId(),
            ]);
        } catch (\Paypal\Exception\PPConnectionEexception $e) {
            var_dump(json_decode($e->getData()));   
        }

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
}
