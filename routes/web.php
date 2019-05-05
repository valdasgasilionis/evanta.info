<?php
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $gateway = new Braintree\Gateway([
        'environment' => config('services.braintree.environment'),
        'merchantId' => config('services.braintree.merchant_id'),
        'publicKey' => config('services.braintree.public_key'),
        'privateKey' => config('services.braintree.private_key')
    ]);

    $token = $gateway->ClientToken()->generate();

    return view('welcome', [
        'token' => $token
    ]);
});

Route::post('/checkout', function(Request $request){

    $gateway = new Braintree\Gateway([
        'environment' => config('services.braintree.environment'),
        'merchantId' => config('services.braintree.merchant_id'),
        'publicKey' => config('services.braintree.public_key'),
        'privateKey' => config('services.braintree.private_key')
    ]);

        $amount = $request->amount;
        $nonce = $request->payment_method_nonce;

        $result = $gateway->transaction()->sale([
            'amount' => $amount,
            'paymentMethodNonce' => $nonce,
            'customer' => [
                'firstName' => 'Valdas',
                'lastName'=> 'Gasilionis',
                'email' => 'admin@evanta.info'
            ],
            'options' => [
                'submitForSettlement' => true
            ]
        ]);

        if ($result->success) {
            $transaction = $result->transaction;
            //header("Location: " . $baseUrl . "transaction.php?id=" . $transaction->id);
            return back()->with('success message','transaction went all well. The transaction ID is:'. $transaction->id);

        } else {
            $errorString = "";

            foreach($result->errors->deepAll() as $error) {
                $errorString .= 'Error: ' . $error->code . ": " . $error->message . "\n";
            }

            /* $_SESSION["errors"] = $errorString;
            header("Location: " . $baseUrl . "index.php"); */
            return back()->withErrors('An error occurred with message:'.$result->message);
        }
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
