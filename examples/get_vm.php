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

$azure = new AzureClient($tenant, $clientId, $clientSecret);
$vm = new VmClient($azure);

$info = $vm->getVm($subscriptionId, $resourceGroup, $vmName);
echo "VM info:\n" . json_encode($info, JSON_PRETTY_PRINT) . PHP_EOL;

$instance = $vm->getInstanceView($subscriptionId, $resourceGroup, $vmName);
echo "Instance view:\n" . json_encode($instance, JSON_PRETTY_PRINT) . PHP_EOL;
