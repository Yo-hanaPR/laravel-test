<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function processing(Request $request){

        if($request['pay-method']=='easymoney'){
            $this->payWithEasyMoney($request);
        }else{
            
            $this->payWithSuperWalletz($request);
        }

        
    }

    public function payWithSuperWalletz(Request $request)
    {

        $request->validate([
            'amount' => 'required|integer|min:1', 
            'currency' => 'required|string|max:3',
            'callback_url' => 'required|url', 
        ]);

        
        $superWalletzUrl = "http://localhost:8000/api/pay"; 

        
        $response = Http::post($superWalletzUrl, [
            'amount' => $request->amount,
            'currency' => $request->currency,
            'callback_url' => $request->callback_url,
        ]);

        
        if ($response->successful()) {
            return response()->json([
                'message' => 'Pago procesado con éxito',
                'transaction_id' => $response->json()['transaction_id'], 
            ]);
        }

        return response()->json([
            'message' => 'Error al procesar el pago con SuperWalletz',
            'error' => $response->body(),
        ], $response->status());
    }

    public function payWithEasyMoney(Request $request)
    {


        try{
            $request->validate([
                'amount' => 'required|integer|min:0.01',
                'currency' => 'required|string|max:3',
            ]);
        }catch(Exception $e){
            $e->getMessage("Error al procesar el pago: No se permiten decimales en el monto");
        }
        $easyMoneyUrl = "http://localhost:8000/process"; 

        

        $response = Http::timeout(120)->post($easyMoneyUrl, [
            'amount' => $request->amount,
            'currency' => $request->currency,
        ]);

       
        if ($response->successful()) {
            return response()->json([
                'message' => 'Pago realizado con éxito',
                'data' => $response->json(),
            ]);
        }

        return response()->json([
            'message' => 'Error al procesar el pago',
            'error' => $response->body(),
        ], $response->status());
    }
}
