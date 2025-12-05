<?php
require __DIR__ . '/../vendor/autoload.php';
use AzureVmSdk\AzureClient;
use AzureVmSdk\NetworkInterfaceClient;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
$tenant = $_ENV['AZURE_TENANT_ID'];
$clientId  = $_ENV['AZURE_CLIENT_ID'];
$clientSecret = $_ENV['AZURE_CLIENT_SECRET'];
$subscriptionId = $_ENV['AZURE_SUBSCRIPTION_ID'];
$resourceGroup = $_ENV['AZURE_RESOURCE_GROUP'];

$azure = new AzureClient($tenant, $clientId, $clientSecret);

$networkInterfaceClient = new NetworkInterfaceClient($azure);

$networkInterface = $networkInterfaceClient->getNetworkInterface($subscriptionId, $resourceGroup, 'my-test-vm-1764394692-nic');
echo json_encode($networkInterface, JSON_PRETTY_PRINT);
