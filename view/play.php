<?php
$resAr = array();
foreach($this->_viewParams['data'] as $key => $value) {
	$resAr[] = "$key=" . urlencode($value);
}

echo implode('&', $resAr);
