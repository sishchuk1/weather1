<?php
use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\Exception as OWMException;
use Http\Factory\Guzzle\RequestFactory;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

require_once __DIR__ . '/bootstrap.php';

$cli = false;
$lf = '<br>';
if (php_sapi_name() === 'cli') {
    $lf = "\n";
    $cli = true;
}

$lang = 'de';

$units = 'metric';

$httpRequestFactory = new RequestFactory();
$httpClient = GuzzleAdapter::createWithConfig([]);

$owm = new OpenWeatherMap($myApiKey, $httpClient, $httpRequestFactory);

$weather = $owm->getWeather('Berlin', $units, $lang);
echo "EXAMPLE 1$lf";

echo $weather->temperature;
echo $lf;

echo $weather->temperature->getFormatted();
echo $lf;

echo $weather->temperature->getValue();
echo $lf;

echo $weather->temperature->getUnit();
echo $lf;


echo 'Current: '.$weather->temperature->now;
echo $lf;

echo 'Minimum: '.$weather->temperature->min;
echo $lf;
echo 'Maximum: '.$weather->temperature->max;
echo $lf;

echo 'Last update: '.$weather->lastUpdate->format('r');
echo $lf;

$weather = $owm->getWeather('London', $units, $lang);
echo "$lf$lf EXAMPLE 2$lf";


echo 'Pressure: '.$weather->pressure;
echo $lf;
echo 'Humidity: '.$weather->humidity;
echo $lf;
echo "$lf$lf EXAMPLE 3$lf";


echo 'Sunrise: '.$weather->sun->rise->format('r');
echo $lf;
echo 'Sunset: '.$weather->sun->set->format('r');
echo $lf;

// Example 4: Get current temperature from coordinates (Greenland :-) ).
$weather = $owm->getWeather(array('lat' => 77.73038, 'lon' => 41.89604), $units, $lang);
echo "$lf$lf EXAMPLE 4$lf";

echo 'Temperature: '.$weather->temperature;
echo $lf;

// Example 5: Get current temperature from city id. The city is an internal id used by OpenWeatherMap. See example 6 too.
$weather = $owm->getWeather(2172797, $units, $lang);
echo "$lf$lf EXAMPLE 5$lf";

echo 'City: '.$weather->city->name;
echo $lf;

echo 'Temperature: '.$weather->temperature;
echo $lf;

// Example 5.1: Get current temperature from zip code (Hyderabad, India).
$weather = $owm->getWeather('zip:500001,IN', $units, $lang);
echo "$lf$lf EXAMPLE 5.1$lf";

echo 'City: '.$weather->city->name;
echo $lf;

echo 'Temperature: '.$weather->temperature;
echo $lf;

// Example 6: Get information about a city.
$weather = $owm->getWeather('Kathmandu', $units, $lang);
echo "$lf$lf EXAMPLE 6$lf";

echo 'Id: '.$weather->city->id;
echo $lf;

echo 'Name: '.$weather->city->name;
echo $lf;

echo 'Lon: '.$weather->city->lon;
echo $lf;

echo 'Lat: '.$weather->city->lat;
echo $lf;

echo 'Country: '.$weather->city->country;
echo $lf;

echo 'Timezone offset to UTC: '.$weather->city->timezone->getOffset(new DateTime("now", new DateTimeZone("UTC")))." seconds";
echo $lf;

// Example 7: Get wind information.
echo "$lf$lf EXAMPLE 7$lf";

echo 'Speed: '.$weather->wind->speed;
echo $lf;

echo 'Direction: '.$weather->wind->direction;
echo $lf;

/*
 * For speed and direction there is a description available, which isn't always translated.
 */

echo 'Speed: '.$weather->wind->speed->getDescription();
echo $lf;

echo 'Direction: '.$weather->wind->direction->getDescription();
echo $lf;

// Example 8: Get information about the clouds.
echo "$lf$lf EXAMPLE 8$lf";

// The number in braces seems to be an indicator how cloudy the sky is.
echo 'Clouds: '.$weather->clouds->getDescription().' ('.$weather->clouds.')';
echo $lf;

// Example 9: Get information about precipitation.
echo "$lf$lf EXAMPLE 9$lf";

echo 'Precipation: '.$weather->precipitation->getDescription().' ('.$weather->precipitation.')';
echo $lf;

// Example 10: Show copyright notice. WARNING: This is no official text. This hint was created by looking at http://www.http://openweathermap.org/copyright .
echo "$lf$lf EXAMPLE 10$lf";

echo $owm::COPYRIGHT;
echo $lf;

// Example 11: Retrieve weather icons.
echo "$lf$lf EXAMPLE 11$lf";
$weather = $owm->getWeather('Berlin');
echo $weather->weather->icon;
echo $lf;
echo $weather->weather->getIconUrl();

// Example 12: Get raw xml data.
echo "$lf$lf EXAMPLE 12$lf";

$xml = $owm->getRawWeatherData('Berlin', $units, $lang, null, 'xml');
if ($cli) {
    echo $xml;
} else {
    echo '<pre><code>'.htmlspecialchars($xml).'</code></pre>';
}
echo $lf;

// Example 13: Get raw json data.
echo "$lf$lf EXAMPLE 13$lf";

$json = $owm->getRawWeatherData('Berlin', $units, $lang, null, 'json');
if ($cli) {
    echo $json;
} else {
    echo '<pre><code>'.htmlspecialchars($json).'</code></pre>';
}
echo $lf;

// Example 14: Get raw html data.
echo "$lf$lf EXAMPLE 14$lf";

echo $owm->getRawWeatherData('Berlin', $units, $lang, null, 'html');
echo $lf;

// Example 15: Error handling.
echo "$lf$lf EXAMPLE 15$lf";

// Try wrong city name.
try {
    $weather = $owm->getWeather('ThisCityNameIsNotValidAndDoesNotExist', $units, $lang);
} catch (OWMException $e) {
    echo $e->getMessage().' (Code '.$e->getCode().').';
    echo $lf;
}

// Try invalid $query.
try {
    $weather = $owm->getWeather(new \DateTime('now'), $units, $lang);
} catch (\Exception $e) {
    echo $e->getMessage().' (Code '.$e->getCode().').';
    echo $lf;
}

// Full error handling would look like this:
try {
    $weather = $owm->getWeather(-1, $units, $lang);
} catch (OWMException $e) {
    echo 'OpenWeatherMap exception: '.$e->getMessage().' (Code '.$e->getCode().').';
    echo $lf;
} catch (\Exception $e) {
    echo 'General exception: '.$e->getMessage().' (Code '.$e->getCode().').';
    echo $lf;
}
