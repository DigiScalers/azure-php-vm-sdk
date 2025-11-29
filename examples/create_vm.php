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
$clientId  = $_ENV['AZURE_CLIENT_ID'];
$clientSecret = $_ENV['AZURE_CLIENT_SECRET'];
$subscriptionId = $_ENV['AZURE_SUBSCRIPTION_ID'];
$resourceGroup = $_ENV['AZURE_RESOURCE_GROUP'];
$vnetName = $_ENV['AZURE_VNET_NAME'];
$subnetName = $_ENV['AZURE_SUBNET_NAME'];
$nsgName = $_ENV['AZURE_NSG_NAME'];
$location = $_ENV['AZURE_REGION']; // Azure region

// VM Configuration
$vmName = 'my-test-vm-' . time(); // Unique VM name
$adminUsername = 'azureuser';
$adminPassword = 'P@ssw0rd123!'; // Must meet Azure password requirements

$subnetId = "/subscriptions/{$subscriptionId}/resourceGroups/{$resourceGroup}/providers/Microsoft.Network/virtualNetworks/{$vnetName}/subnets/{$subnetName}";

// VM Specifications
$vmSize = 'Standard_DC16ads_cc_v5';
$diskSizeGB = 128;   // OS disk size in GB

// Operating System
// Using Gen2 image for DC-series VMs which require Hypervisor Generation 2
$os = [
    'publisher' => 'MicrosoftWindowsDesktop',
    'offer' => 'Windows-11',
    'sku' => 'win11-23h2-ent',
    'version' => 'latest'
];

try {
    // Initialize Azure Client
    $azure = new AzureClient($tenant, $clientId, $clientSecret);
    $vmClient = new VmClient($azure);
    $networkInterfaceClient = new NetworkInterfaceClient($azure);

    echo "Creating VM: {$vmName}\n";
    echo "Location: {$location}\n";
    echo "Specifications: {$vmSize}, {$diskSizeGB}GB disk\n";
    echo "\nThis may take several minutes...\n\n";

    // --- Logic moved from VmClient::createVM ---

    // Prepare Network Interface name and ID
    $nicName = "{$vmName}-nic";
    $nicId = "/subscriptions/{$subscriptionId}/resourceGroups/{$resourceGroup}/providers/Microsoft.Network/networkInterfaces/{$nicName}";
    
    $publicIpName = "{$vmName}-pip";
    $domainNameLabel = strtolower($vmName . '-' . substr(md5(uniqid()), 0, 6)); // Ensure uniqueness
    
    // Build Public IP payload using Builder pattern
    $publicIpPayload = (new PublicIpBuilder())
        ->setLocation($location)
        ->setSku('Standard')
        ->setAllocationMethod('Static')
        ->setDomainNameLabel($domainNameLabel)
        ->build();

    echo "Creating Public IP: {$publicIpName}...\n";
    $networkInterfaceClient->createPublicIp(
        $subscriptionId,
        $resourceGroup,
        $location,
        $publicIpName,
        $publicIpPayload
    );
    
    // Wait for Public IP to be provisioned
    echo "Waiting for Public IP to be provisioned...\n";
    $maxRetries = 20;
    $retryDelay = 3;
    $publicIpProvisioned = false;

    for ($i = 0; $i < $maxRetries; $i++) {
        $pip = $networkInterfaceClient->getPublicIp($subscriptionId, $resourceGroup, $publicIpName);
        $state = $pip['properties']['provisioningState'] ?? 'Unknown';
        echo "Public IP State: {$state}\n";

        if ($state === 'Succeeded') {
            $publicIpProvisioned = true;
            break;
        }

        if ($state === 'Failed' || $state === 'Canceled') {
            throw new \Exception("Public IP creation failed with state: {$state}");
        }

        sleep($retryDelay);
    }

    if (!$publicIpProvisioned) {
        throw new \Exception("Public IP creation timed out");
    }

    $publicIpId = "/subscriptions/{$subscriptionId}/resourceGroups/{$resourceGroup}/providers/Microsoft.Network/publicIPAddresses/{$publicIpName}";

    // Build Network Interface payload using Builder pattern
    $builder = (new NetworkInterfaceBuilder())
        ->setLocation($location)
        ->setSubnet($subnetId)
        ->setPublicIp($publicIpId);

    // Attach NSG if provided
    if ($nsgName !== null) {
        $builder->setNetworkSecurityGroup($subscriptionId, $resourceGroup, $nsgName);
    }

    $nicPayload = $builder->build();

    // Create Network Interface
    echo "Creating Network Interface: {$nicName}...\n";
    $networkInterfaceClient->createNetworkInterface(
        $subscriptionId,
        $resourceGroup,
        $nicName,
        $nicPayload
    );

    // Wait for Network Interface to be provisioned
    echo "Waiting for Network Interface to be provisioned...\n";
    $nicProvisioned = false;

    for ($i = 0; $i < $maxRetries; $i++) {
        $nic = $networkInterfaceClient->getNetworkInterface($subscriptionId, $resourceGroup, $nicName);
        $state = $nic['properties']['provisioningState'] ?? 'Unknown';
        echo "Network Interface State: {$state}\n";

        if ($state === 'Succeeded') {
            $nicProvisioned = true;
            break;
        }

        if ($state === 'Failed' || $state === 'Canceled') {
            throw new \Exception("Network Interface creation failed with state: {$state}");
        }

        sleep($retryDelay);
    }

    if (!$nicProvisioned) {
        throw new \Exception("Network Interface creation timed out");
    }

    // Build VM payload using Builder pattern
    $vmPayload = (new VmBuilder())
        ->setLocation($location)
        ->setVmSize($vmSize)
        ->setImageReference($os)
        ->setOsDisk("{$vmName}_OsDisk", $diskSizeGB, 'Standard_LRS')
        ->setOsProfile(substr($vmName, 0, 15), $adminUsername, $adminPassword)
        ->addNetworkInterface($nicId, true)
        ->build();

    // Create VM
    echo "Creating Virtual Machine...\n";
    $result = $vmClient->createOrUpdateVm($subscriptionId, $resourceGroup, $vmName, $vmPayload);

    // -------------------------------------------

    echo "VM Creation Response:\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

    // Get VM details
    echo "Fetching VM details...\n";
    $vmDetails = $vmClient->getVm($subscriptionId, $resourceGroup, $vmName);
    
    echo "\nVM Details:\n";
    echo "Name: " . ($vmDetails['name'] ?? 'N/A') . "\n";
    echo "Location: " . ($vmDetails['location'] ?? 'N/A') . "\n";
    echo "VM Size: " . ($vmDetails['properties']['hardwareProfile']['vmSize'] ?? 'N/A') . "\n";
    echo "Provisioning State: " . ($vmDetails['properties']['provisioningState'] ?? 'N/A') . "\n";

    // Get instance view to check power state
    echo "\nFetching instance view...\n";
    $instanceView = $vmClient->getInstanceView($subscriptionId, $resourceGroup, $vmName);
    
    if (isset($instanceView['statuses'])) {
        echo "\nVM Status:\n";
        foreach ($instanceView['statuses'] as $status) {
            echo "- " . ($status['code'] ?? 'N/A') . ": " . ($status['displayStatus'] ?? 'N/A') . "\n";
        }
    }

    echo "\n✓ VM created successfully!\n";
    echo "\nConnection Details:\n";
    echo "Username: {$adminUsername}\n";
    echo "Password: {$adminPassword}\n";
    
    echo "\nNote: A public IP has been created. You can find the IP address in the Azure Portal.\n";
    echo "To get the public IP programmatically, you would need to query the Network Interface resource.\n";

} catch (\Exception $e) {
    echo "Error creating VM: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
