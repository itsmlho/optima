<?php
require_once 'app/Config/Database.php';
$db = \Config\Database::connect();
$spks = $db->table('spk')->orderBy('id','DESC')->limit(3)->get()->getResultArray();
foreach($spks as $spk){
    echo json_encode($spk) . "\n";
}
echo "\nTable structure:\n";
$fields = $db->getFieldData('spk');
foreach($fields as $field){
    echo $field->name . " (" . $field->type . ")\n";
}
