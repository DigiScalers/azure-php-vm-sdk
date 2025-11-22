<?php
require 'vendor/autoload.php';
use AzureVmSdk\AzureClient;
use AzureVmSdk\VmClient;

$tenant = 'your-tenant-id';
$clientId = 'your-client-id';
$clientSecret = 'your-client-secret';
$subscriptionId = 'your-subscription-id';
$resourceGroup = 'your-resource-group-name';

$azure = new AzureClient($tenant, $clientId, $clientSecret);
$vm = new VmClient($azure);

$list = $vm->listVms($subscriptionId, $resourceGroup);
foreach ($list as $item) {
    echo json_encode($item) . PHP_EOL;
}
