<?php

namespace Ispahbod\IpManager;

class IpManager
{
    public static function isValidIp($ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }
    public static function getIpVersion($ip): ?int
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return 4;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return 6;
        }

        return null;
    }
    public static function getSubnetMask(string $ip): ?string
    {
        $parts = explode('/', $ip);
        return $parts[1] ?? null;
    }
    public static function getNetworkPortion(string $ip): ?string
    {
        $subnetMask = $this->getSubnetMask($ip);
        if ($subnetMask === null) {
            return null;
        }

        $parts = explode('/', $ip);
        $ipAddress = $parts[0];
        return long2ip((ip2long($ipAddress) & (-1 << (32 - (int)$subnetMask))));
    }
    public static function areIpsInSameSubnet(string $ip1, string $ip2): bool
    {
        $network1 = $this->getNetworkPortion($ip1);
        $network2 = $this->getNetworkPortion($ip2);
        return $network1 === $network2;
    }

    public static function generateFakeIpV4(): string
    {
        $ip = mt_rand(1, 254) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(1, 254);
        return $ip;
    }

    public static function generateFakeIpV6(): string
    {
        $ip = implode(':', array_map(function () {
            return dechex(mt_rand(0, 65535));
        }, range(1, 8)));
        return $ip;
    }
}