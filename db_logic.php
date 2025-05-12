<?php
use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;


function getShiftsCollection() {
    $client = new Client("mongodb://localhost:27017");
    $database = $client->selectDatabase('laba2db');
    $collection = $database->selectCollection('shifts');

    return $collection;
}


function findRoomsByNurse($nurseName) {
    $collection = getShiftsCollection();
    $filter = ['nurses' => $nurseName];
    $options = ['projection' => ['_id' => 0, 'rooms' => 1]];
    $results = $collection->find($filter, $options);
    return $results->toArray();
}


function findNursesByDepartment($department) {
    $collection = getShiftsCollection();
    $filter = ['department' => $department];
    $options = ['projection' => ['_id' => 0, 'nurses' => 1]];
    $results = $collection->find($filter, $options);

    $allNurses = [];
    foreach ($results as $document) {
         if (isset($document['nurses'])) {
             $allNurses = array_merge($allNurses, $document['nurses']->getArrayCopy());
         }
    }
    $uniqueNurses = array_unique($allNurses);
    sort($uniqueNurses);
    return $uniqueNurses;
}


function findShiftsByShiftAndDepartment($shift, $department) {
    $collection = getShiftsCollection();
    $filter = ['shift' => $shift, 'department' => $department];
    $options = ['sort' => ['date' => 1]];
    $results = $collection->find($filter, $options);
    return $results->toArray();
}

?>
