<?php

namespace Ispahbod\IpManager;

class IpManager
{
    public function isValidIp($ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }
    public function getIpVersion($ip): ?int
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return 4;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return 6;
        }

        return null;
    }
    public function getSubnetMask(string $ip): ?string
    {
        $parts = explode('/', $ip);
        return $parts[1] ?? null;
    }
    public function getNetworkPortion(string $ip): ?string
    {
        $subnetMask = $this->getSubnetMask($ip);
        if ($subnetMask === null) {
            return null;
        }

        $parts = explode('/', $ip);
        $ipAddress = $parts[0];
        return long2ip((ip2long($ipAddress) & (-1 << (32 - (int)$subnetMask))));
    }
    public function areIpsInSameSubnet(string $ip1, string $ip2): bool
    {
        $network1 = $this->getNetworkPortion($ip1);
        $network2 = $this->getNetworkPortion($ip2);
        return $network1 === $network2;
    }
}