<?php
namespace AzureVmSdk;

class PublicIpBuilder {
    private string $location;
    private string $skuName = 'Standard';
    private string $allocationMethod = 'Static';
    private ?string $domainNameLabel = null;

    /**
     * Set the Azure region/location
     */
    public function setLocation(string $location): self {
        $this->location = $location;
        return $this;
    }

    /**
     * Set the SKU name (default: Standard)
     */
    public function setSku(string $skuName): self {
        $this->skuName = $skuName;
        return $this;
    }

    /**
     * Set the IP allocation method (default: Static)
     */
    public function setAllocationMethod(string $method): self {
        $this->allocationMethod = $method;
        return $this;
    }

    /**
     * Set the DNS domain name label (optional)
     */
    public function setDomainNameLabel(string $domainNameLabel): self {
        $this->domainNameLabel = $domainNameLabel;
        return $this;
    }

    /**
     * Build and return the Public IP payload
     */
    public function build(): array {
        if (!isset($this->location)) {
            throw new \InvalidArgumentException('Location is required');
        }

        $payload = [
            'location' => $this->location,
            'sku' => [
                'name' => $this->skuName
            ],
            'properties' => [
                'publicIPAllocationMethod' => $this->allocationMethod
            ]
        ];

        // Add DNS settings if domain name label is provided
        if ($this->domainNameLabel !== null) {
            $payload['properties']['dnsSettings'] = [
                'domainNameLabel' => $this->domainNameLabel
            ];
        }

        return $payload;
    }
}
