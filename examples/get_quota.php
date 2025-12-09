<?php
require __DIR__ . '/../vendor/autoload.php';
use AzureVmSdk\AzureClient;
use AzureVmSdk\VmClient;
use AzureVmSdk\VmBuilder;
use AzureVmSdk\NetworkInterfaceClient;
use AzureVmSdk\NetworkInterfaceBuilder;
use AzureVmSdk\PublicIpBuilder;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
$tenant = $_ENV['AZURE_TENANT_ID'];
$clientId = $_ENV['AZURE_CLIENT_ID'];
$clientSecret = $_ENV['AZURE_CLIENT_SECRET'];
$subscriptionId = $_ENV['AZURE_SUBSCRIPTION_ID'];

// Initialize Azure Client
$azure = new AzureClient($tenant, $clientId, $clientSecret);
$vmClient = new VmClient($azure);

$usage = $vmClient->getComputeUsages($subscriptionId, 'eastus');

file_put_contents('test.json', json_encode($usage));

foreach ($usage as $quota) {
    echo $quota['name']['value'] . ': ' . $quota['currentValue'] . '/' . $quota['limit'] . PHP_EOL;
}
