<?php
use Illuminate\Http\Request;
use App\Rent;
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
Route::get('/', function(){
       return view ('welcome');
});

/* Route::resource('rents','RentController'); */
Route::get('/rents', function() {
    $rentals = Rent::all();
    return view('rents.index',compact('rentals'));
});

Route::post('/rents', function(Request $request) {
    if (auth()->check()) {
       $rental = new Rent;
    $rental->start = $request->start;
    $rental->end = $request->end;
    $rental->price = $request->price;

    $rental->save();

    return back(); 
    }    
});

Route::post('/rents/{id}', function($id) {
    if (auth()->check()) {
        $rent = Rent::find($id);
        if ($rent->payed === 0) {
            $rent->payed = true;
        }else {
            $rent->payed = false;
        }
        
        $rent->save(); 

        return back();
    }
});

Route::get('/hosted', function() {
    $gateway = new Braintree\Gateway([
        'environment' => config('services.braintree.environment'),
        'merchantId' => config('services.braintree.merchant_id'),
        'publicKey' => config('services.braintree.public_key'),
        'privateKey' => config('services.braintree.private_key')
    ]);

    $token = $gateway->ClientToken()->generate();

    return view ('hosted', [
        'token' => $token,
    ]);
});
Route::get('rents/{rent}/edit', function($rent) {
    $gateway = new Braintree\Gateway([
        'environment' => config('services.braintree.environment'),
        'merchantId' => config('services.braintree.merchant_id'),
        'publicKey' => config('services.braintree.public_key'),
        'privateKey' => config('services.braintree.private_key')
    ]);

    $token = $gateway->ClientToken()->generate();

    $rental = Rent::where('id', $rent)->get();

    return view('rents.edit', [
        'token' => $token,
        'rental' => $rental
    ]);
});

Route::get('/pay', function () {
    $gateway = new Braintree\Gateway([
        'environment' => config('services.braintree.environment'),
        'merchantId' => config('services.braintree.merchant_id'),
        'publicKey' => config('services.braintree.public_key'),
        'privateKey' => config('services.braintree.private_key')
    ]);

    $token = $gateway->ClientToken()->generate();

    return view('pay', [
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

        $firstName = $request->first_name;
        $lastName = $request->last_name;
        $email = $request->customer_email;
        $id = $request->id_number;

        $nonce = $request->payment_method_nonce;

        $result = $gateway->transaction()->sale([
            'amount' => $amount,
            'paymentMethodNonce' => $nonce,
            'customer' => [
                'firstName' => $firstName,
                'lastName'=> $lastName,
                'email' => $email
            ],
            'options' => [
                'submitForSettlement' => true
            ]
        ]);

        if ($result->success) {
            $transaction = $result->transaction;
            //header("Location: " . $baseUrl . "transaction.php?id=" . $transaction->id);
            $rent = Rent::find($id);
            $rent->reserved = true;
            $rent->save();
            return redirect('/')->with('success message','transaction went all well. The transaction ID is:'. $transaction->id);

        } else {
            $errorString = "";

            foreach($result->errors->deepAll() as $error) {
                $errorString .= 'Error: ' . $error->code . ": " . $error->message . "\n";
            }

            /* $_SESSION["errors"] = $errorString;
            header("Location: " . $baseUrl . "index.php"); */
            return redirect('/')->withErrors('An error occurred with message:'.$result->message);
        }
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
