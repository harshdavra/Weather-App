    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="{{asset('images/default.png')}}">
        <link rel="stylesheet" href="css/style.css">
        <title>Wheather App</title>
    </head>

    <body>
        <div class="mainConatiner">
            <div class="left">
                @if(session('error'))
                <script>
                    alert("{{session('error')}}");
                </script>
                @endif

                @if(session('weatherdata'))
                <script>
                    console.log(@json(session('weatherdata')));
                </script>
                @endif

                @if(session('airdata'))
                <script>
                    console.log(@json(session('airdata')));
                </script>
                @endif

                @if(session('forecastdata'))
                <script>
                    console.log(@json(session('forecastdata')));
                </script>
                @endif
   
            
                <form action="{{ url('/searchCity') }}" method="GET" onsubmit="showLoading()">
                     @csrf
                    <div class="input">
                            <input type="search" id="cityinput" placeholder="Search places" name="city" onkeyup="searchcity()" value="{{ session('searched_city') }}">
                            <button type="submit" class="search-icon">🔍</button>
                    </div>
                </form>
                <div class="img">
                    <img src="{{$temp['iconPath']}}" alt="" class="main-img" id="weather-icon">
                </div>
                <div class="weather-info">
                    <h1 class="temperature-c">{{$temp['tempCelsius']}}°C</h1>
                    <h1 class="temperature-f">{{$temp['tempFahrenheit']}}°F</h1>
                    <p>{{$temp['day']}},<span>{{$temp['time']}}</span></p>
                    <p id="description">{{$temp['desc']}}</p>
                </div>
                <div class="location">
                    <img src="images/location-sign-svgrepo-com.png" class="location-svg">
                <div class="city-container">
                    <p id="cityname">{{$weatherData['name']}},</p>
                    <p id="countryname">{{$weatherData['sys']['country']}}</p>
                </div>
                </div>
            </div>
            <div class="right">
                <div class="tabs">
                    <div class="tab1">
                        <button id="TodayBtn" onclick="ShowForecaste('today')">Today</button>
                        <button id="WeekBtn" onclick="ShowForecaste('week')">Week</button>
                    </div>
                    <div class="tab2">
                        <button id="CelsiusBtn" onclick="SetUnit('c')">°C</button>
                        <button id="FahrenheitBtn" onclick="SetUnit('f')">°F</button>
                    </div>
                    
                </div>
                <div class="data-forecast">
                <div class="today-container">
                <h2 id="hourly">Hourly Forecast</h2>
                <div id="today" class="forecast-items">
                    @foreach ($forecastTimesData as $forecast)
                    <div class="forecast-item">
                      
                  
                            <h5 class="forecast-date">{{ $forecast['time'] }}</h5>
                
                           <div class="image-forecast">
                            <img src="{{ asset($forecast['icon']) }}" alt="Weather Icon" class="forecast-item-img">
                        </div>
                  
                            <h5 class="temperature-c">{{ $forecast['temperature_c'] }}°C</h5>
                            <h5 class="temperature-f">{{ $forecast['temperature_f'] }}°F</h5>
                        </div>
                
                @endforeach
                </div>
            </div>
            <div class="weekly-container">
                <h2 id="weekly">Weekly Forecast</h2>
                <div id="week" class="forecast-items">
                    @foreach ($forecastDayData as $forecast)
                    <div class="forecast-item">
                        <h5 class="forecast-date">{{ $forecast['day'] }}</h5>
                        <div class="image-forecast">
                            <img src="{{ asset($forecast['icon']) }}" alt="Weather Icon" class="forecast-item-img">
                        </div>
                        <h5 class="temperature-c">{{ $forecast['temperature_c'] }}°C</h5>
                        <h5 class="temperature-f">{{ $forecast['temperature_f'] }}°F</h5>
                    </div>
                   @endforeach
                    </div>
                 </div>
                </div>
                <div class="highlights">
                    <h2>Today's Highlights</h2>
                    <div class="card-container">
                        <div class="card">
                            <p>Humidity</p>
                            <div class="small-container">
                                <h3 id="humidity">{{$temp['humidity']}}%</h3>
                                <span>{{$temp['humiditylevel']}}</span>
                            </div>
                        </div>
                        <div class="card">
                            <p>Real Feel</p>
                            <div class="small-container">
                                <h3 class="temperature-c">{{$temp['tempCelsiusFeels']}}°C</h3>
                                <h3 class="temperature-f">{{$temp['tempFahrenheitFeels']}}°F</h3>
                                <span>{{$temp['feellevel']}}</span>
                            </div>
                        </div>
                        <div class="card">
                            <p>Visibility</p>
                            <div class="small-container">
                                <h3 id="visibility">{{$temp['visibilityKm']}} km</h3>
                                <span>{{$temp['visibilitylevel']}}</span>
                            </div>
                        </div>
                        <div class="card">
                            <p>Wind Status</p>
                            <div class="small-container">
                                <h3 id="wind-speed">{{$temp['windspeed']}} km/h</h3>
                                <span>{{$temp['windDirection']}}</span>
                            </div>
                        </div>
                        <div class="card">
                            <p>Air Quality</p>
                            <div class="small-container">
                                <h3 id="air-quality">{{$temp['airindex']}}</h3>
                                <span id="quality-disc">{{$temp['airquality']}}</span>
                            </div>
                        </div>
                        <div class="card">
                            <p>Sunrise & Sunset</p>
                            <div class="small-container">
                                <h3  id="sunrise">⬆️ {{$temp['sunriseTime']}}</h3>
                                <h3 id="sunset">⬇️ {{$temp['sunsetTime']}}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <script>
function ShowForecaste(type) {
    let todaybtn = document.getElementById("TodayBtn");
    let weekbtn = document.getElementById("WeekBtn");
    let weekly = document.getElementById("weekly");
    let hourly = document.getElementById("hourly");

    // Show or hide forecast sections
    document.getElementById("today").style.display = type === "today" ? "grid" : "none";
    document.getElementById("week").style.display = type === "week" ? "grid" : "none";

    // Update styles for the active tab
    if (type === "today") {
        todaybtn.style.backgroundColor = "#000000"; // Active color
        todaybtn.style.borderRadius = "7px";
        todaybtn.style.color = "white";
        weekbtn.style.border = "2px solid rgba(99, 99, 99, 0.518)";
        hourly.style.display = "block";
        weekbtn.style.backgroundColor = "transparent"; // Reset to default
        weekbtn.style.borderRadius = "7px";
        weekly.style.display = "none";
        weekbtn.style.color = "black";
    } else if (type === "week") {
        weekbtn.style.backgroundColor = "#000000"; // Active color
        weekbtn.style.borderRadius = "7px";
        weekly.style.display = "block";
        weekbtn.style.color = "white";
        todaybtn.style.backgroundColor = "transparent"; // Reset to default
        todaybtn.style.borderRadius = "7px";
        todaybtn.style.border = "2px solid rgba(99, 99, 99, 0.518)";
        todaybtn.style.color = "black";
        hourly.style.display = "none";
    }
}

function SetUnit(unit) {
    let celsiusbtn = document.getElementById("CelsiusBtn");
    let fahrenheitbtn = document.getElementById("FahrenheitBtn"); // Fixed spelling
    let temp_c = document.getElementsByClassName("temperature-c");
    let temp_f = document.getElementsByClassName("temperature-f");

    if (unit === 'c') {
        for (let i = 0; i < temp_c.length; i++) {
            temp_c[i].style.display = "block";
            celsiusbtn.style.backgroundColor = "black";
            celsiusbtn.style.color = "white";
            celsiusbtn.style.borderRadius = "50px";
            celsiusbtn.style.border = "2px solid #cacaca9a";
        }
        for (let i = 0; i < temp_f.length; i++) {
            temp_f[i].style.display = "none";
            fahrenheitbtn.style.borderRadius = "50px";
            fahrenheitbtn.style.border = "2px solid #cacaca9a";
            fahrenheitbtn.style.backgroundColor = "white";
            fahrenheitbtn.style.color = "black";
        }
    } 
    else if (unit === 'f') {
        for (let i = 0; i < temp_f.length; i++) {
            temp_f[i].style.display = "block";
            fahrenheitbtn.style.backgroundColor = "black";
            fahrenheitbtn.style.color = "white";
            fahrenheitbtn.style.borderRadius = "50px";
            fahrenheitbtn.style.border = "2px solid #cacaca9a";
        }
        for (let i = 0; i < temp_c.length; i++) {
            temp_c[i].style.display = "none";
            celsiusbtn.style.backgroundColor = "white";
            celsiusbtn.style.color = "black";
            celsiusbtn.style.borderRadius = "50px";
            celsiusbtn.style.border = "2px solid #cacaca9a";
        }
    }
}

window.onload = function () {
    ShowForecaste("today");
    SetUnit("c");
};
const input = document.getElementById('cityinput');
const resultsBox = document.getElementById('results-box');

let debounceTimer;

input.addEventListener('input', function () {
    clearTimeout(debounceTimer);

    const val = this.value.trim();
    if (val.length < 3) {
        resultsBox.style.display = 'none';
        return;
    }

    debounceTimer = setTimeout(() => {
         resultsBox.innerHTML = `
            <div class="list-group-item text-muted">
                <span class="spinner-border spinner-border-sm me-2"></span>
                Searching...
            </div>`;
        resultsBox.style.display = 'block';

        fetch(`/api/autocomplete?q=${encodeURIComponent(val)}`)
            .then(res => res.json())
            .then(data => {
                resultsBox.innerHTML = '';
                resultsBox.style.display = 'block';

                if (!data.features || data.features.length === 0) {
                    resultsBox.innerHTML = `
                        <div class="list-group-item text-muted">
                            No destinations found
                        </div>`;
                    return;
                }

                data.features.forEach(feature => {
                    const city = feature.properties.name;
                    const country = feature.properties.country || '';
                    const fullText = country ? `${city}, ${country}` : city;

                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'list-group-item list-group-item-action border-0';
                    btn.innerHTML = `
                        <i class="bi bi-geo-alt-fill text-muted me-2"></i>
                        <strong>${city}</strong>
                        ${country ? `<small class="text-secondary">, ${country}</small>` : ''}
                    `;

                    btn.onclick = () => {
                        input.value = fullText;
                        resultsBox.style.display = 'none';
                    };

                    resultsBox.appendChild(btn);
                });
            })
            .catch(() => {
                resultsBox.innerHTML = `
                    <div class="list-group-item text-danger">
                        Error loading destinations
                    </div>`;
            });
    }, 120); // debounce delay
});

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('.destination')) {
        resultsBox.style.display = 'none';
    }
});


    </script>
    </html>