<?php
namespace AzureVmSdk;

class VmBuilder {
    private string $location;
    private string $vmSize;
    private array $imageReference;
    private ?array $osDisk = null;
    private ?array $osProfile = null;
    private array $networkInterfaces = [];

    /**
     * Set the Azure region/location
     */
    public function setLocation(string $location): self {
        $this->location = $location;
        return $this;
    }

    /**
     * Set the VM size
     */
    public function setVmSize(string $vmSize): self {
        $this->vmSize = $vmSize;
        return $this;
    }

    /**
     * Set the image reference (OS image)
     */
    public function setImageReference(array $imageReference): self {
        $this->imageReference = $imageReference;
        return $this;
    }

    /**
     * Configure the OS disk
     * 
     * @param string $name Disk name
     * @param int $sizeGB Disk size in GB
     * @param string $storageType Storage account type (e.g., 'Standard_LRS', 'Premium_LRS')
     * @param string $caching Caching mode (default: 'ReadWrite')
     */
    public function setOsDisk(string $name, int $sizeGB, string $storageType = 'Standard_LRS', string $caching = 'ReadWrite'): self {
        $this->osDisk = [
            'name' => $name,
            'caching' => $caching,
            'createOption' => 'FromImage',
            'managedDisk' => [
                'storageAccountType' => $storageType
            ],
            'diskSizeGB' => $sizeGB
        ];
        return $this;
    }

    /**
     * Set the OS profile (credentials)
     * 
     * @param string $computerName Computer name
     * @param string $adminUsername Admin username
     * @param string $adminPassword Admin password
     */
    public function setOsProfile(string $computerName, string $adminUsername, string $adminPassword): self {
        $this->osProfile = [
            'computerName' => $computerName,
            'adminUsername' => $adminUsername,
            'adminPassword' => $adminPassword
        ];
        return $this;
    }

    /**
     * Add a network interface
     * 
     * @param string $nicId Network interface resource ID
     * @param bool $primary Whether this is the primary NIC
     */
    public function addNetworkInterface(string $nicId, bool $primary = true): self {
        $this->networkInterfaces[] = [
            'id' => $nicId,
            'properties' => [
                'primary' => $primary
            ]
        ];
        return $this;
    }

    /**
     * Build and return the VM payload
     */
    public function build(): array {
        if (!isset($this->location)) {
            throw new \InvalidArgumentException('Location is required');
        }
        if (!isset($this->vmSize)) {
            throw new \InvalidArgumentException('VM size is required');
        }
        if (!isset($this->imageReference)) {
            throw new \InvalidArgumentException('Image reference is required');
        }
        if ($this->osDisk === null) {
            throw new \InvalidArgumentException('OS disk configuration is required');
        }
        if ($this->osProfile === null) {
            throw new \InvalidArgumentException('OS profile is required');
        }
        if (empty($this->networkInterfaces)) {
            throw new \InvalidArgumentException('At least one network interface is required');
        }

        return [
            'location' => $this->location,
            'properties' => [
                'hardwareProfile' => [
                    'vmSize' => $this->vmSize
                ],
                'storageProfile' => [
                    'imageReference' => $this->imageReference,
                    'osDisk' => $this->osDisk
                ],
                'osProfile' => $this->osProfile,
                'networkProfile' => [
                    'networkInterfaces' => $this->networkInterfaces
                ]
            ]
        ];
    }
}
