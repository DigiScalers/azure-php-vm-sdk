<?php
namespace AzureVmSdk;

class VmClient {
    private AzureClient $client;
    public function __construct(AzureClient $client) {
        $this->client = $client;
    }

    public function listVms(string $subscriptionId, string $resourceGroup): array {
        $path = "/subscriptions/{$subscriptionId}/resourceGroups/{$resourceGroup}/providers/Microsoft.Compute/virtualMachines";
        $response = $this->client->request('GET', $path, []);
        return $response['value'] ?? [];
    }

    public function getVm(string $subscriptionId, string $resourceGroup, string $vmName): array {
        $path = "/subscriptions/{$subscriptionId}/resourceGroups/{$resourceGroup}/providers/Microsoft.Compute/virtualMachines/{$vmName}";
        return $this->client->request('GET', $path, []);
    }

    public function getInstanceView(string $subscriptionId, string $resourceGroup, string $vmName): array {
        $path = "/subscriptions/{$subscriptionId}/resourceGroups/{$resourceGroup}/providers/Microsoft.Compute/virtualMachines/{$vmName}/instanceView";
        return $this->client->request('GET', $path, []);
    }

    public function createOrUpdateVm(string $subscriptionId, string $resourceGroup, string $vmName, array $vmPayload): array {
        $path = "/subscriptions/{$subscriptionId}/resourceGroups/{$resourceGroup}/providers/Microsoft.Compute/virtualMachines/{$vmName}";
        return $this->client->request('PUT', $path, [], $vmPayload);
    }

    public function deleteVm(string $subscriptionId, string $resourceGroup, string $vmName): array {
        $path = "/subscriptions/{$subscriptionId}/resourceGroups/{$resourceGroup}/providers/Microsoft.Compute/virtualMachines/{$vmName}";
        return $this->client->request('DELETE', $path, []);
    }

    public function startVm(string $subscriptionId, string $resourceGroup, string $vmName): array {
        $path = "/subscriptions/{$subscriptionId}/resourceGroups/{$resourceGroup}/providers/Microsoft.Compute/virtualMachines/{$vmName}/start";
        return $this->client->request('POST', $path, []);
    }

    public function powerOffVm(string $subscriptionId, string $resourceGroup, string $vmName): array {
        $path = "/subscriptions/{$subscriptionId}/resourceGroups/{$resourceGroup}/providers/Microsoft.Compute/virtualMachines/{$vmName}/powerOff";
        return $this->client->request('POST', $path, []);
    }

    public function restartVm(string $subscriptionId, string $resourceGroup, string $vmName): array {
        $path = "/subscriptions/{$subscriptionId}/resourceGroups/{$resourceGroup}/providers/Microsoft.Compute/virtualMachines/{$vmName}/restart";
        return $this->client->request('POST', $path, []);
    }

    public function deallocateVm(string $subscriptionId, string $resourceGroup, string $vmName): array {
        $path = "/subscriptions/{$subscriptionId}/resourceGroups/{$resourceGroup}/providers/Microsoft.Compute/virtualMachines/{$vmName}/deallocate";
        return $this->client->request('POST', $path, []);
    }
}