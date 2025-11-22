<?php
require __DIR__ . '/../vendor/autoload.php';
use AzureVmSdk\AzureClient;
use AzureVmSdk\VmClient;

$tenant = 'your-tenant-id';
$clientId = 'your-client-id';
$clientSecret = 'your-client-secret';
$subscriptionId = 'your-subscription-id';
$resourceGroup = 'your-resource-group-name';
$vmName = 'your-vm-name';

$azure = new AzureClient($tenant, $clientId, $clientSecret);
$vm = new VmClient($azure);

$info = $vm->getVm($subscriptionId, $resourceGroup, $vmName);
echo "VM info:\n" . json_encode($info, JSON_PRETTY_PRINT) . PHP_EOL;

$instance = $vm->getInstanceView($subscriptionId, $resourceGroup, $vmName);
echo "Instance view:\n" . json_encode($instance, JSON_PRETTY_PRINT) . PHP_EOL;
