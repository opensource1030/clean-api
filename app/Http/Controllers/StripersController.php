<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use WA\DataStore\Payment\Payment;
use WA\DataStore\Payment\PaymentTransformer;
use WA\Repositories\Payment\PaymentInterface;

/**
 * Payment resource.
 *
 * @Resource("payment", uri="/payment")
 */
class PaymentsController extends FilteredApiController
{
    /**
     * Payment Controller constructor.
     *
     * @param PaymentInterface $payment
     * @param Request $request
     */
    public function __construct(PaymentInterface $payment, Request $request)
    {
        parent::__construct($payment, $request);
        $this->payment = $payment;
    }
    
    /**
     * Create a new Payment.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        try {
            // apiKey, card, exp_month, exp_year, amount, currency.
            $data = $request->all()['attributes'];

            $user = \WA\DataStore\User\User::find($data->userId);
            if ($user == null) {
                $error['errors']['payments'] = Lang::get('messages.NotExistClass', ['class' => 'User']);
                return response()->json($error)->setStatusCode($this->status_codes['notexists']);
            }

            $order = \WA\DataStore\Order\Order::find($data->orderId);
            if ($order == null) {
                $error['errors']['payments'] = Lang::get('messages.NotExistClass', ['class' => 'Order']);
                return response()->json($error)->setStatusCode($this->status_codes['notexists']);
            }

            if (!isset($data->card) || !isset($data->exp_month) || !isset($data->exp_year) || !isset($data->apiKey) || !isset($data->amount) || !isset($data->currency)) {
                $error['errors']['payments'] = "You must send all the attributes required.";
                return response()->json($error)->setStatusCode($this->status_codes['conflict']);   
            }

            \Stripe\Stripe::setApiKey($data->apiKey);
            $myCard = array('number' => $data->card, 'exp_month' => $data->exp_month, 'exp_year' => $data->exp_year);
            $charge = \Stripe\Charge::create([
                'card' => $myCard,
                'amount' => $data->amount,
                'currency' => $data->currency,
                //'description' => 'Clean Platform'
            ]);

            $attributes['success'] = true;
            $attributes['details'] = json_encode($charge);
            $attributes['transactionId'] = $charge->id;
            $attributes['userId'] = $data->userId;
            $attributes['orderId'] = $data->orderId;

            $pay = $payment->create($attributes);
            if(!$pay) {
                $error['errors']['payments'] = "The payment has not been completed due a creation error.",
                    ['class' => 'Address', 'option' => 'created', 'include' => '']);
                return response()->json($error)->setStatusCode($this->status_codes['conflict']);
            }

            return $this->response()->item($pay, new PaymentTransformer(),
                ['key' => 'payments'])->setStatusCode($this->status_codes['created']);
        } catch (Stripe_CardError $e) {
            $error['errors']['payments'] = Lang::get('messages.NotOptionIncludeClass',
                    ['class' => 'Address', 'option' => 'created', 'include' => '']);
            return response()->json($error)->setStatusCode($this->status_codes['conflict']); 
        } catch (\Exception $e) {
            $error['errors']['payments'] = Lang::get('messages.NotOptionIncludeClass',
                    ['class' => 'Payment', 'option' => 'created', 'include' => '']);
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }

    /**
     * Delete a Payment.
     *
     * @param $id
     */
    public function delete($id)
    {
        $payment = Payment::find($id);
        if ($payment != null) {
            $this->payment->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'Payment']);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $payment = Payment::find($id);
        if ($payment == null) {
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'Payment']);
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}
