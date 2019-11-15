<?php

error_reporting(0);

require __DIR__ . '/vendor/autoload.php';

const AUTH_FILE = 'forge-radio-2236af57181e.secret.json';
const SHOW_MASTER_SPREADSHEET_ID = '1xBPosA0PAF3rGvkDSoYLA0AIXmEj4Pc9fP5vV9r06B0';
const SHOW_SCHEDULE_SHEET_VALUES_RANGE = ["'Shows'!A:C", "'Shows'!F:F"];

const DAYS = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');

function create_authed_client() {
    $client = new Google_Client();
    $client->setApplicationName('Forge Radio');
    $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
    $client->setAuthConfig(AUTH_FILE);

    return $client;
}

function get_show_data($client) {
    $service = new Google_Service_Sheets($client);

    $params = array(
        'ranges' => SHOW_SCHEDULE_SHEET_VALUES_RANGE
    );

    $response = $service->spreadsheets_values->batchGet(SHOW_MASTER_SPREADSHEET_ID, $params);
    $values = $response->getValueRanges();

    $show_data = $values[0]['values'];
    $presenter_data = $values[1]['values'];

    // Merge the two ranges together into one collection of show slots.
    $full_data = array();
    foreach($show_data as $index=>$show) {
        $row = array_merge($show, $presenter_data[$index]);
        array_push($full_data, $row);
    }

    // Slice away the column headings.
    $full_data = array_slice($full_data, 2);

    // Filter the data to only full show slots
    // A row has 4 items (Day, Time Range, Name, Presenter) if it's full so filter on that.
    function is_valid_show_slot($show_slot) {
        return (count($show_slot) == 4);
    }

    $full_data = array_filter($full_data, 'is_valid_show_slot');

    // Translate a day name into a number.
    function dayname_to_daynumber($day) {
        return array_search($day, DAYS);
    }
    
    function prepare_show_slot($show_slot) {
        $show_slot = array_map('trim', $show_slot);
        list($start, $end) = explode('-', $show_slot[1]);

        return array(
            'title' => $show_slot[2],
            'daysOfWeek' => array(dayname_to_daynumber($show_slot[0])),
            'startTime' => $start,
            'endTime' => $end,
            'showPresenter' => $show_slot[3]
        );
    }

    return array_values(array_map('prepare_show_slot', $ful_data));
}

$client = create_authed_client();
$data = get_show_data($client);

header('Content-Type: application/json');
echo json_encode($data);

?>