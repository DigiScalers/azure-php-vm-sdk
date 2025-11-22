<?php
use PHPUnit\Framework\TestCase;
use AzureVmSdk\AzureClient;
use AzureVmSdk\VmClient;

class VmClientTest extends TestCase {
    public function testListVmsReturnsArray() {
        $mock = $this->createMock(AzureClient::class);
        $subscription = 'sub123';
        $rg = 'rg1';
        $path = "/subscriptions/{$subscription}/resourceGroups/{$rg}/providers/Microsoft.Compute/virtualMachines";

        $mock->expects($this->once())
            ->method('request')
            ->with('GET', $path, [])
            ->willReturn(['value' => [['name' => 'vm1']]]);

        $vmc = new VmClient($mock);
        $list = $vmc->listVms($subscription, $rg);
        $this->assertIsArray($list);
        $this->assertCount(1, $list);
        $this->assertEquals('vm1', $list[0]['name']);
    }

    public function testGetVm() {
        $mock = $this->createMock(AzureClient::class);
        $subscription = 'sub123';
        $rg = 'rg1';
        $vmName = 'myvm';
        $path = "/subscriptions/{$subscription}/resourceGroups/{$rg}/providers/Microsoft.Compute/virtualMachines/{$vmName}";

        $mock->expects($this->once())
            ->method('request')
            ->with('GET', $path, [])
            ->willReturn(['name' => $vmName]);

        $vmc = new VmClient($mock);
        $resp = $vmc->getVm($subscription, $rg, $vmName);
        $this->assertIsArray($resp);
        $this->assertEquals($vmName, $resp['name']);
    }

    public function testStartVmCallsPost() {
        $mock = $this->createMock(AzureClient::class);
        $subscription = 'sub123';
        $rg = 'rg1';
        $vmName = 'myvm';
        $path = "/subscriptions/{$subscription}/resourceGroups/{$rg}/providers/Microsoft.Compute/virtualMachines/{$vmName}/start";

        $mock->expects($this->once())
            ->method('request')
            ->with('POST', $path, [])
            ->willReturn(['status' => 'Accepted']);

        $vmc = new VmClient($mock);
        $resp = $vmc->startVm($subscription, $rg, $vmName);
        $this->assertEquals('Accepted', $resp['status']);
    }

    public function testCreateOrUpdateVmUsesPut() {
        $mock = $this->createMock(AzureClient::class);
        $subscription = 'sub123';
        $rg = 'rg1';
        $vmName = 'myvm';
        $payload = ['location' => 'eastus'];
        $path = "/subscriptions/{$subscription}/resourceGroups/{$rg}/providers/Microsoft.Compute/virtualMachines/{$vmName}";

        $mock->expects($this->once())
            ->method('request')
            ->with('PUT', $path, [], $payload)
            ->willReturn(['name' => $vmName]);

        $vmc = new VmClient($mock);
        $resp = $vmc->createOrUpdateVm($subscription, $rg, $vmName, $payload);
        $this->assertEquals($vmName, $resp['name']);
    }
}
