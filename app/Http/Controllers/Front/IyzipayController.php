<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\HeaderLogo;
use App\Models\Language;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class IyzipayController extends Controller
{
    public function iyzipay(Request $request) {
        $logos     = HeaderLogo::first();
        $sections  = Section::all();
        $language  = Language::get();
        $condition = $request->query('condition');

        if (Session::has('order_id')) {
            return view('front.iyzipay.iyzipay', compact('condition', 'sections', 'language', 'logos'));

        } else {
            return redirect('cart'); // redirect user to cart.blade.php page
        }
    }

    // Make an iyzipay payment (redirect the user to iyzico payment gateway with the order details)
    public function pay(Request $request) {
        $condition = $request->query('condition');
        if (!in_array($condition, ['new', 'old'])) {
            $condition = 'new';
        }

        $orderDetails = \App\Models\Order::with('orders_products')->where('id', Session::get('order_id'))->first()->toArray();

        $nameArr = explode(' ', $orderDetails['name']);

        $options = \App\Models\Iyzipay::options();
        $request = new \Iyzipay\Request\CreatePayWithIyzicoInitializeRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        // $request->setConversationId("123456789"); // dummy data
        $request->setConversationId(Session::get('order_id'));
        $request->setPrice(Session::get('grand_total'));

        $request->setPaidPrice(Session::get('grand_total'));
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        // $request->setBasketId("B67832"); // dummy data
        $request->setBasketId(Session::get('order_id'));
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl("https://www.merchant.com/callback");
        $request->setEnabledInstallments(array(2, 3, 6, 9));
        $buyer = new \Iyzipay\Model\Buyer();
        // $buyer->setId("BY789"); // dummy data
        $buyer->setId($orderDetails['user_id']); // real data (our order details)    // user_id
        // $buyer->setName("John"); // dummy data
        $buyer->setName($nameArr[0]); // real data (our order details)
        // $buyer->setSurname("Doe"); // dummy data
        $buyer->setSurname($nameArr[1] ?? 'Not set'); // real data (our order details)
        $buyer->setGsmNumber("+905350000000"); // dummy data
        // $buyer->setEmail("email@email.com"); // dummy data
        $buyer->setEmail($orderDetails['email']); // real data (our order details)
        $buyer->setIdentityNumber("74300864791"); // dummy data
        // $buyer->setLastLoginDate("2015-10-05 12:43:35"); // dummy data
        $buyer->setLastLoginDate("");
        $buyer->setRegistrationDate("");
        $buyer->setRegistrationAddress($orderDetails['address']);
        $buyer->setIp("");
        $buyer->setCity($orderDetails['city']); // real data (our order details)
        // $buyer->setCountry("Turkey"); // dummy data
        $buyer->setCountry($orderDetails['country']); // real data (our order details)
        // $buyer->setZipCode("34732"); // dummy data
        $buyer->setZipCode($orderDetails['pincode']); // real data (our order details)
        $request->setBuyer($buyer); // dummy data
        $shippingAddress = new \Iyzipay\Model\Address(); // dummy data
        // $shippingAddress->setContactName("Jane Doe"); // dummy data
        $shippingAddress->setContactName($orderDetails['name']); // real data (our order details)
        // $shippingAddress->setCity("Istanbul"); // dummy data
        $shippingAddress->setCity($orderDetails['city']); // real data (our order details)
        // $shippingAddress->setCountry("Turkey"); // dummy data
        $shippingAddress->setCountry($orderDetails['country']); // real data (our order details)
        // $shippingAddress->setAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1"); // dummy data
        $shippingAddress->setAddress($orderDetails['address']); // real data (our order details)
        // $shippingAddress->setZipCode("34742"); // dummy data
        $shippingAddress->setZipCode($orderDetails['pincode']); // real data (our order details)
        $request->setShippingAddress($shippingAddress); // dummy data
        $billingAddress = new \Iyzipay\Model\Address(); // dummy data
        // $billingAddress->setContactName("Jane Doe"); // dummy data
        $billingAddress->setContactName($orderDetails['name']); // real data (our order details)
        // $billingAddress->setCity("Istanbul"); // dummy data
        $billingAddress->setCity($orderDetails['city']); // real data (our order details)
        // $billingAddress->setCountry("Turkey"); // dummy data
        $billingAddress->setCountry($orderDetails['country']); // real data (our order details)
        // $billingAddress->setAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1"); // dummy data
        $billingAddress->setAddress($orderDetails['address']); // real data (our order details)
        // $billingAddress->setZipCode("34742"); // dummy data
        $billingAddress->setZipCode($orderDetails['pincode']); // real data (our order details)
        $request->setBillingAddress($billingAddress); // dummy data
        $basketItems = array(); // dummy data
        $firstBasketItem = new \Iyzipay\Model\BasketItem(); // dummy data
        // $firstBasketItem->setId("BI101"); // dummy data
        $firstBasketItem->setId(Session::get('order_id')); // real data (our order details)    // 'order_id' was stored in the Session in checkout() method in Front/ProductsController.php    // Interacting With The Session: Retrieving Data: https://laravel.com/docs/9.x/session#retrieving-data
        // $firstBasketItem->setName("Binocular"); // dummy data
        $firstBasketItem->setName("Order ID: " . Session::get('order_id'));
        // $firstBasketItem->setCategory1("Collectibles"); // dummy data
        $firstBasketItem->setCategory1("Multi-vendor E-commerce Application Product"); // real data (our order details)
        // $firstBasketItem->setCategory2("Accessories"); // dummy data
        $firstBasketItem->setCategory2("");
        $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL); // dummy data
        // $firstBasketItem->setPrice("0.3"); // dummy data
        $firstBasketItem->setPrice(Session::get('grand_total'));
        $basketItems[0] = $firstBasketItem; // dummy data



        $request->setBasketItems($basketItems); // dummy data
        # make request
        $payWithIyzicoInitialize = \Iyzipay\Model\PayWithIyzicoInitialize::create($request, $options);

        // Convert the JSON Response (string/text) to a PHP array
        $paymentResponse = (array) $payWithIyzicoInitialize;


        foreach ($paymentResponse as $key => $response) {
            $response_decode = json_decode($response);

            $pay_url = $response_decode->payWithIyzicoPageUrl;

            break;
        }



        return redirect($pay_url);
    }

}
