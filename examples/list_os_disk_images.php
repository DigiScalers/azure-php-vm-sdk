<?php

require __DIR__ . '/../vendor/autoload.php';

use AzureVmSdk\AzureClient;
use AzureVmSdk\VmClient;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Azure credentials and configuration
$tenant = $_ENV['AZURE_TENANT_ID'];
$clientId = $_ENV['AZURE_CLIENT_ID'];
$clientSecret = $_ENV['AZURE_CLIENT_SECRET'];
$subscriptionId = $_ENV['AZURE_SUBSCRIPTION_ID'];

$azure = new AzureClient($tenant, $clientId, $clientSecret);
$vmClient = new VmClient($azure);

// Example: Find available versions for Ubuntu Server 22.04 LTS
echo "Finding available Ubuntu Server 22.04 LTS images in eastus...\n";

// 1. We know the publisher is 'Canonical' and offer is '0001-com-ubuntu-server-jammy'
// Let's list the SKUs for this offer
$publisher = 'Canonical';
$offer = '0001-com-ubuntu-server-jammy';

try {
    echo "Fetching SKUs for {$publisher} / {$offer}...\n";
    $skus = $vmClient->getAvailableOSTypes($subscriptionId, 'eastus', $publisher, $offer);

    foreach ($skus as $sku) {
        $skuName = $sku['name'];
        echo "Found SKU: {$skuName}\n";

        // List versions for the first SKU found
        echo "  Fetching versions for SKU: {$skuName}...\n";
        $versions = $vmClient->getAvailableOSTypes($subscriptionId, 'eastus', $publisher, $offer, $skuName);
        foreach (array_slice($versions, 0, 3) as $version) {
            echo "    - Version: " . $version['name'] . "\n";
        }
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}