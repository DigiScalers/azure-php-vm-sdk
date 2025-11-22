<?php
require 'vendor/autoload.php';
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

$list = $vm->listVms($subscriptionId, $resourceGroup);
foreach ($list as $item) {
    echo json_encode($item) . PHP_EOL;
}
