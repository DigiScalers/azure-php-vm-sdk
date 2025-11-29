<?php
namespace AzureVmSdk;

class NetworkInterfaceBuilder {
    private string $location;
    private string $subnetId;
    private ?string $publicIpId = null;
    private ?string $nsgId = null;
    private string $privateIpAllocationMethod = 'Dynamic';
    private string $ipConfigName = 'ipconfig1';

    /**
     * Set the Azure region/location
     */
    public function setLocation(string $location): self {
        $this->location = $location;
        return $this;
    }

    /**
     * Set the subnet ID
     */
    public function setSubnet(string $subnetId): self {
        $this->subnetId = $subnetId;
        return $this;
    }

    /**
     * Set the public IP address ID (optional)
     */
    public function setPublicIp(string $publicIpId): self {
        $this->publicIpId = $publicIpId;
        return $this;
    }

    /**
     * Set the Network Security Group (optional)
     */
    public function setNetworkSecurityGroup(string $subscriptionId, string $resourceGroup, string $nsgName): self {
        $this->nsgId = "/subscriptions/{$subscriptionId}/resourceGroups/{$resourceGroup}/providers/Microsoft.Network/networkSecurityGroups/{$nsgName}";
        return $this;
    }

    /**
     * Set the private IP allocation method (default: Dynamic)
     */
    public function setPrivateIpAllocationMethod(string $method): self {
        $this->privateIpAllocationMethod = $method;
        return $this;
    }

    /**
     * Set the IP configuration name (default: ipconfig1)
     */
    public function setIpConfigName(string $name): self {
        $this->ipConfigName = $name;
        return $this;
    }

    /**
     * Build and return the Network Interface payload
     */
    public function build(): array {
        if (!isset($this->location)) {
            throw new \InvalidArgumentException('Location is required');
        }
        if (!isset($this->subnetId)) {
            throw new \InvalidArgumentException('Subnet ID is required');
        }

        $payload = [
            'location' => $this->location,
            'properties' => [
                'ipConfigurations' => [
                    [
                        'name' => $this->ipConfigName,
                        'properties' => [
                            'subnet' => [
                                'id' => $this->subnetId
                            ],
                            'privateIPAllocationMethod' => $this->privateIpAllocationMethod
                        ]
                    ]
                ]
            ]
        ];

        // Add public IP if provided
        if ($this->publicIpId !== null) {
            $payload['properties']['ipConfigurations'][0]['properties']['publicIPAddress'] = [
                'id' => $this->publicIpId
            ];
        }

        // Add NSG if provided
        if ($this->nsgId !== null) {
            $payload['properties']['networkSecurityGroup'] = [
                'id' => $this->nsgId
            ];
        }

        return $payload;
    }
}
