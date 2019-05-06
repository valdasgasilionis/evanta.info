<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Checkout</title>
   <!-- Styles -->
   <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }
        #card-number, #cvv, #expiration-date, #postal-code {
            background: #fff;
            height: 38px;
            border: 1px solid #ced4de;
            padding: .375rem .75rem;
            border-radius: .25rem;
        }
        #button {
            padding: .375rem .75rem;
        }
        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
  </head>
  <body>
      <div class="container bg-info">
            <form action="{{url('/checkout')}}" id="my-sample-form" method="post">
                @csrf
                <div class="form-group">
                    <div class="row">
                        <div class="col">
                            <label for="first-name">First name</label>
                            <input type="text" class="form-control" name="first_name" placeholder="...">
                        </div>
                        <div class="col">
                            <label for="last-name">Last name</label>
                            <input type="text" class="form-control" name="last_name" placeholder="...">
                        </div>
                       
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                         <div class="col">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="customer_email" placeholder="..@.">
                        </div>
                        <div class="col">
                            <label for="amount">Amount</label>
                            <input type="text" class="form-control" id="amount" name="amount" placeholder="$$$">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">                        
                        <div class="col">
                            <label for="card-number">Card Number</label>
                            <div id="card-number"></div>
                        </div>
                        <div class="col">
                            <label for="cvv">CVV</label>
                            <div id="cvv"></div>    
                        </div>
                        <div class="col">
                            <label for="expiration-date">Expiration Date</label>
                            <div id="expiration-date"></div>
                        </div>
                        <div class="col">
                            <label for="postal-code">Postal code</label>
                            <div id="postal-code"></div>
                        </div>
                    </div>                
                </div>

                <div class="form-group">
                    <div class="row" >
                        <div class="col" id="button">
                            <input type="submit" class="btn btn-warning" value="Pay" disabled />
                        </div>
                    </div>
                </div>
    {{-- hidden field - nonce  --}}
                <input id="nonce" name="payment_method_nonce" type="hidden" />

            </form>
      </div>

    <script src="https://js.braintreegateway.com/web/3.44.2/js/client.min.js"></script>
    <script src="https://js.braintreegateway.com/web/3.44.2/js/hosted-fields.min.js"></script>
    <script>
      var form = document.querySelector('#my-sample-form');
      var submit = document.querySelector('input[type="submit"]');

      braintree.client.create({
        authorization: '{{$token}}'
      }, function (clientErr, clientInstance) {
        if (clientErr) {
          console.error(clientErr);
          return;
        }

        // This example shows Hosted Fields, but you can also use this
        // client instance to create additional components here, such as
        // PayPal or Data Collector.

        braintree.hostedFields.create({
          client: clientInstance,
          styles: {
            'input': {
              'font-size': '14px'
            },
            'input.invalid': {
              'color': 'red'
            },
            'input.valid': {
              'color': 'green'
            }
          },
          fields: {
            number: {
              selector: '#card-number',
              placeholder: '4111 1111 1111 1111'
            },
            cvv: {
              selector: '#cvv',
              placeholder: '123'
            },
            expirationDate: {
              selector: '#expiration-date',
              placeholder: '10/2019'
            },
            postalCode: {
                selector: '#postal-code',
                placeholder: '00000'
            }
          }
        }, function (hostedFieldsErr, hostedFieldsInstance) {
          if (hostedFieldsErr) {
            console.error(hostedFieldsErr);
            return;
          }

          submit.removeAttribute('disabled');

          form.addEventListener('submit', function (event) {
            event.preventDefault();

            hostedFieldsInstance.tokenize(function (tokenizeErr, payload) {
              if (tokenizeErr) {
                console.error(tokenizeErr);
                return;
              }

              // If this was a real integration, this is where you would
              // send the nonce to your server.
              /*  console.log('Got a nonce: ' + payload.nonce); */
              document.querySelector('#nonce').value = payload.nonce;
                  form.submit();             
            });
          }, false);
        });
      });
    </script>
  </body>
</html>