<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function fetchWeather(Request $request)
    {
        $city = $request->query('city', 'Surat');  
        $apikey = env('OPENWEATHER_API_KEY');

        $weatherFetch = Http::get("https://api.openweathermap.org/data/2.5/weather", [
            'q'     => $city,
            'units' => 'metric',
            'appid' => $apikey 
        ]);

        if ($weatherFetch->successful()) {
            $weatherData = $weatherFetch->json();

            $desc=$weatherData['weather'][0]['description']?? "No Data";

            $iconcode=$weatherData['weather'][0]['icon']?? "01d";
            $myIcon=[
               '01d'=>'clearday.png',
               '01n'=>'clearnight.png',
               '02d'=>'fewcloudday.png',
               '02n'=>'fewcloudnight.png',
               '03d'=>'fewcloudday.png',
               '03n'=>'fewcloudnight.png',
               '04d'=>'brokencloud.png',
               '04n'=>'brokencloud.png',
               '09d'=>'showerrain.png',
               '09n'=>'showerrain.png',
               '10d'=>'rainyday.png',
               '10n'=>'rainynight.png',
               '11d'=>'thunderstrom.png',
               '11n'=>'thunderstrom.png',
               '13d'=>'snow.png',
               '13n'=>'snow.png',
               '50d'=>'mist.png',
               '50n'=>'mist.png'
            ];

            $customIcon=$myIcon[$iconcode] ?? 'default.png';
            $iconPath="images/".$customIcon;
       
            $windspeed=isset($weatherData['wind']['speed'])
            ? number_format(($weatherData['wind']['speed']),1)
            : 0;
            $degree=$weatherData['wind']['deg'] ?? 0;
            $directions=[
               "N","NNE","NE","ENE","E","ESE","SE","SSE",
               "S","SSW","SW","WSW","W","WNW","NW","NNW"
            ];
            $windDegree=round($degree/22.5)%16;
            $windDirection=$directions[$windDegree] ?? "N";

            $dt=time();
            $timezoneoffset=$weatherData['timezone']?? 0;
            $localTime=$dt+$timezoneoffset;
            $day=date('l',$localTime);
            $time=date('h:i A',$localTime);
            
            $sunrise=$weatherData['sys']['sunrise'] ?? 0;
            $sunset=$weatherData['sys']['sunset'] ?? 0;
            $sunriseLocal=$sunrise ? $sunrise+$timezoneoffset : 0;
            $sunsetLocal=$sunset ? $sunset+$timezoneoffset : 0;
            $sunriseTime=$sunriseLocal ? date('h:i A', $sunriseLocal) : "No Data";
            $sunsetTime=$sunsetLocal ? date('h:i A', $sunsetLocal) : "No Data";

            $humidity=$weatherData['main']['humidity'] ?? 0;
            if($humidity>=70){
               $humiditylevel="High 💧";
            }
            elseif($humidity>=50){
               $humiditylevel="Medium 👌";
            }
            elseif($humidity>=30){
               $humiditylevel="Normal 👍";
            }
            elseif($humidity>=20){
               $humiditylevel="Low 👎";
            }
            else{
               $humiditylevel="Very Low 🌵";
            }
            
           
            $visibility=$weatherData['visibility'] ?? "N/A";
            if(is_numeric($visibility)){
               $visibilityKm=number_format($visibility/1000,1);
            }
            else{
               $visibilityKm="N/A";
            }

            if($visibilityKm==="N/A"){
               $visibilitylevel="❓";
            }
            elseif($visibilityKm>=10){
               $visibilitylevel="Excellent ✨";
            }
            elseif($visibilityKm>=6){
               $visibilitylevel="Good 🌤️";
            }
            elseif($visibilityKm>=4){
               $visibilitylevel="Average 🌥️";
            }
            elseif($visibilityKm>=2){
               $visibilitylevel="Poor 🌁";
            }
            else{
               $visibilitylevel="Very Poor 😨";
            }
   
            $tempCelsiusFeels=round($weatherData['main']['feels_like'])?? "Unknown";
            if($tempCelsiusFeels>=40){
               $feellevel="Very Hot 🔥";
            }
            elseif($tempCelsiusFeels>=30){
               $feellevel="Hot ☀️";
            }
            elseif($tempCelsiusFeels>=20){
               $feellevel="Normal ⛅";
            }
            elseif($tempCelsiusFeels>=10){
               $feellevel="Cold 🧊";
            }
            else{
               $feellevel="Freezing 🥶 ";
            }
            $tempFahrenheitFeels=round(($tempCelsiusFeels * 9/5)+32) ?? "Unknown";
            $tempCelsius=round($weatherData['main']['temp']) ?? "Unknown";
            $tempFahrenheit=round(($tempCelsius * 9/5)+32) ?? "Unknown";

            $lat=$weatherData['coord']['lat'] ?? null;
            $lon=$weatherData['coord']['lon'] ?? null;

            if($lat!==null && $lon!==null){
            $airFetch= Http::get("http://api.openweathermap.org/data/2.5/air_pollution",[
               'lat'=>$lat,
               'lon'=>$lon,
               'appid'=>$apikey
            ]);

            if($airFetch->successful()){
               $airData=$airFetch->json();

               $airindex=$airData['list'][0]['main']['aqi'] ?? "N/A";
               switch($airindex){
                  case 1:$airquality='Good 😊';
                  break;
                  case 2:$airquality='Fair 🙂';
                  break;
                  case 3:$airquality='Moderate 😐';
                  break;
                  case 4:$airquality='Poor 😷';
                  break;
                  case 5:$airquality='Very Poor 😡';
                  break;
                  default:$airquality='Unknown ❔';
               }
            }else{
               $airquality="Data Not ❌";
            }
         }
          else{
               $airquality="Location Not ❌";
         }

            $forecastFetch=Http::get("http://api.openweathermap.org/data/2.5/forecast",[
               'lat'=>$lat,
               'lon'=>$lon,
               'appid' => $apikey, 
               'units'=>'metric'
            ]);
            if($forecastFetch->successful()){
               $forecastData=$forecastFetch->json();
               $forecastTimesData=[];
               $forecastDayData=[];
               $processedDays=[];

   
               foreach($forecastData['list'] as $forecast){
                  $forecastTimestamp=$forecast['dt'];
                  $dayName=date('l',$forecastTimestamp + $timezoneoffset);
                  $datekey=date('Y-m-d',$forecastTimestamp+$timezoneoffset);
                  $formatTime=date('h:i A',$forecastTimestamp+$timezoneoffset);
                    
                     $iconCode=$forecast['weather'][0]['icon']?? "01d";
                     $customIconForecast=$myIcon[$iconCode];
                     $iconPathForecast="images/".$customIconForecast;
                     if(count($forecastTimesData)<6){
                     $forecastTimesData[]=[
                        'time'=>$formatTime,
                        'temperature_c'=>round($forecast['main']['temp']) ?? "No Data",
                        'temperature_f'=>round(($forecast['main']['temp']*9/5)+32) ?? "No Data",
                        'icon'=>$iconPathForecast,
                     ];
                    }

                    if(!in_array($datekey,$processedDays) && count($forecastDayData)<6){
                     $forecastDayData[]= [
                     'day'=>$dayName,
                     'temperature_c'=>round($forecast['main']['temp']) ?? "No Data",
                     'temperature_f'=>round(($forecast['main']['temp']*9/5)+32) ?? "No Data",
                     'icon'=>$iconPathForecast,
                     ];
                     $processedDays[]=$datekey;
                    }
                  if(count($forecastTimesData)==6 && count($forecastDayData)==6){
                     break;
                  }
                    
               }  
            }


            $temp=[
               'tempFahrenheit'=>$tempFahrenheit,
               'tempCelsius'=>$tempCelsius,
               'tempFahrenheitFeels'=>$tempFahrenheitFeels,
               'tempCelsiusFeels'=>$tempCelsiusFeels,
               'feellevel'=>$feellevel,
               'humidity'=>$humidity,
               'humiditylevel'=>$humiditylevel,
               'visibilityKm'=>$visibilityKm,
               'visibilitylevel'=>$visibilitylevel,
               'time'=>$time,
               'day'=>$day,
               'sunriseTime'=>$sunriseTime,
               'sunsetTime'=>$sunsetTime,
               'airquality'=>$airquality,
               'airindex'=>$airindex,
               'windspeed'=>$windspeed,
               'windDirection'=>$windDirection,
               'iconPath'=>$iconPath,
               'desc'=>$desc,
            ];

            session(['forecastdata' => $forecastData]);
            session(['airdata' => $airData]);
            session(['weatherdata' => $weatherData]);
            session(['searched_city' => $city]);
            return view('weather', compact('weatherData','temp','forecastTimesData','forecastDayData')); 
        } else {
            return back()->with('error', 'City not found! Please enter a valid city name.');
        }
    }
    public function autocomplete(Request $request)
{
    $query = trim($request->query('q'));

    if (strlen($query) < 3) {
        return response()->json(['features' => []]);
    }

    try {
        $response = Http::timeout(5)
            ->withoutVerifying()
            ->get('https://photon.komoot.io/api/', [
                'q' => $query,
                'limit' => 5
            ]);

        return $response->successful()
            ? $response->json()
            : response()->json(['features' => []], 500);

    } catch (\Exception $e) {
        \Log::error("Autocomplete failed: ".$e->getMessage());
        return response()->json(['features' => []], 503);
    }
}
}
