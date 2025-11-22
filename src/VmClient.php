<?php
namespace AzureVmSdk;

class VmClient {
    private AzureClient $client;
    public function __construct(AzureClient $client) {
        $this->client = $client;
    }
    public function startVm(string $subscriptionId, string $resourceGroup, string $vmName) {
        $path = "/subscriptions/{$subscriptionId}/resourceGroups/{$resourceGroup}/providers/Microsoft.Compute/virtualMachines/{$vmName}/start";
        return $this->client->request('POST', $path, []);
    }

    public function listVms(string $subscriptionId, string $resourceGroup): array {
        $path = "/subscriptions/{$subscriptionId}/resourceGroups/{$resourceGroup}/providers/Microsoft.Compute/virtualMachines";
        $response = $this->client->request('GET', $path, []);
        return $response['value'] ?? [];
    }
}