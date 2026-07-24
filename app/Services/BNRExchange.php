<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class BNRExchange
{
    public function getRate(string $currency): ?float
    {
        if($currency === 'RON'){
            return 1.0;
        }
        return Cache::remember("bnr_rate_{$currency}_" . now()->format('Y-m-d'), now()->addHours(12), function () use($currency){
            try{
                $response = Http::timeout(5)->get('https://www.bnr.ro/nbrfxrates.xml');
                if(! $response->ok()){
                    return null;
                }
                $xml = simplexml_load_string($response->body());
                foreach($xml->Body->Cube->Rate as $rate){
                    if((string) $rate['currency'] === $currency){
                        return(float) $rate;
                    }
                }
            }
            catch(\Throwable $e){
                return null;
            }
            return null;
        });

    }
}