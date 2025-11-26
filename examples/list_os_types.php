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
$clientId  = $_ENV['AZURE_CLIENT_ID'];
$clientSecret = $_ENV['AZURE_CLIENT_SECRET'];
$subscriptionId = $_ENV['AZURE_SUBSCRIPTION_ID'];

$azure = new AzureClient($tenant, $clientId, $clientSecret);
$vmClient = new VmClient($azure);

$location = 'eastus'; // Change to your preferred location

echo "=== Available OS Types for {$location} ===\n\n";

// Example 1: Get all publishers
echo "1. Getting all publishers...\n";
$publishers = $vmClient->getAvailableOSTypes($subscriptionId, $location);
echo "Found " . count($publishers) . " publishers\n";
echo "First 5 publishers:\n";
foreach (array_slice($publishers, 0, 5) as $publisher) {
    echo "  - " . ($publisher['name'] ?? 'N/A') . "\n";
}
echo "\n";

// Example 2: Get offers for Windows Server
echo "2. Getting offers for MicrosoftWindowsServer...\n";
$offers = $vmClient->getAvailableOSTypes($subscriptionId, $location, 'MicrosoftWindowsServer');
echo "Found " . count($offers) . " offers\n";
foreach ($offers as $offer) {
    echo "  - " . ($offer['name'] ?? 'N/A') . "\n";
}
echo "\n";

// Example 3: Get SKUs for Windows Server
echo "3. Getting SKUs for MicrosoftWindowsServer / WindowsServer...\n";
$skus = $vmClient->getAvailableOSTypes($subscriptionId, $location, 'MicrosoftWindowsServer', 'WindowsServer');
echo "Found " . count($skus) . " SKUs\n";
foreach ($skus as $sku) {
    echo "  - " . ($sku['name'] ?? 'N/A') . "\n";
}
echo "\n";

// Example 4: Get offers for Ubuntu (Canonical)
echo "4. Getting offers for Canonical (Ubuntu)...\n";
$ubuntuOffers = $vmClient->getAvailableOSTypes($subscriptionId, $location, 'Canonical');
echo "Found " . count($ubuntuOffers) . " offers\n";
foreach ($ubuntuOffers as $offer) {
    echo "  - " . ($offer['name'] ?? 'N/A') . "\n";
}
echo "\n";

// Example 5: Get SKUs for Ubuntu Server
echo "5. Getting SKUs for Canonical / UbuntuServer...\n";
$ubuntuSkus = $vmClient->getAvailableOSTypes($subscriptionId, $location, 'Canonical', 'UbuntuServer');
echo "Found " . count($ubuntuSkus) . " SKUs\n";
foreach (array_slice($ubuntuSkus, 0, 10) as $sku) {
    echo "  - " . ($sku['name'] ?? 'N/A') . "\n";
}
echo "\n";

echo "=== Common Publishers ===\n";
echo "Windows: MicrosoftWindowsServer\n";
echo "Ubuntu: Canonical\n";
echo "Red Hat: RedHat\n";
echo "CentOS: OpenLogic\n";
echo "Debian: Debian\n";
echo "SUSE: SUSE\n";
