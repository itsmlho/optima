<?php
$html = file_get_contents(__DIR__ . '/../app/Views/marketing/customer_management.php');
$lines = explode("\n", $html);

$depth = 0;
$in_customer_modal = false;
$customer_modal_depth = 0;

for ($i = 0; $i < count($lines); $i++) {
    $line = $lines[$i];
    $lineNum = $i + 1;
    
    if (strpos($line, 'id="customerDetailModal"') !== false) {
        echo "Line $lineNum: FOUND customerDetailModal (depth=$depth)\n";
        $in_customer_modal = true;
        $customer_modal_depth = $depth;
    }
    
    if (strpos($line, 'id="addLocationModal"') !== false) {
        $status = $in_customer_modal ? 'YES - NESTED INSIDE!' : 'NO - Independent';
        echo "Line $lineNum: FOUND addLocationModal (depth=$depth, in_customer_modal=$status)\n";
    }
    
    if (strpos($line, 'id="addContractModal"') !== false) {
        $status = $in_customer_modal ? 'YES - NESTED INSIDE!' : 'NO - Independent';
        echo "Line $lineNum: FOUND addContractModal (depth=$depth, in_customer_modal=$status)\n";
    }
    
    if (strpos($line, 'id="addCustomerModal"') !== false) {
        $status = $in_customer_modal ? 'YES - NESTED INSIDE!' : 'NO - Independent';
        echo "Line $lineNum: FOUND addCustomerModal (depth=$depth, in_customer_modal=$status)\n";
    }
    
    // Count opening/closing divs
    $opens = substr_count($line, '<div');
    $closes = substr_count($line, '</div>');
    $depth += $opens - $closes;
    
    if ($in_customer_modal && $depth <= $customer_modal_depth) {
        echo "Line $lineNum: CLOSED customerDetailModal (depth=$depth)\n";
        $in_customer_modal = false;
    }
}

echo "\nFinal depth: $depth\n";
