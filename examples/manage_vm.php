<?php
require __DIR__ . '/../vendor/autoload.php';
use AzureVmSdk\AzureClient;
use AzureVmSdk\VmClient;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
$tenant = $_ENV['AZURE_TENANT_ID'];
$clientId  = $_ENV['AZURE_CLIENT_ID'];
$clientSecret = $_ENV['AZURE_CLIENT_SECRET'];
$subscriptionId = $_ENV['AZURE_SUBSCRIPTION_ID'];
$resourceGroup = $_ENV['AZURE_RESOURCE_GROUP'];
$vmName = 'test-vm';

$azure = new AzureClient($tenant, $clientId, $clientSecret);
$vm = new VmClient($azure);

// Start VM
$start = $vm->startVm($subscriptionId, $resourceGroup, $vmName);
echo "Start response: " . json_encode($start) . PHP_EOL;

// Restart VM
$restart = $vm->restartVm($subscriptionId, $resourceGroup, $vmName);
echo "Restart response: " . json_encode($restart) . PHP_EOL;

// Power off VM
$powerOff = $vm->powerOffVm($subscriptionId, $resourceGroup, $vmName);
echo "PowerOff response: " . json_encode($powerOff) . PHP_EOL;

// Deallocate VM
$dealloc = $vm->deallocateVm($subscriptionId, $resourceGroup, $vmName);
echo "Deallocate response: " . json_encode($dealloc) . PHP_EOL;

// Delete VM (uncomment to use)
$del = $vm->deleteVm($subscriptionId, $resourceGroup, $vmName);
echo "Delete response: " . json_encode($del) . PHP_EOL;
