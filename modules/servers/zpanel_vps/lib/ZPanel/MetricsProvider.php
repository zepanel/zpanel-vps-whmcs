<?php

namespace WHMCS\Module\Server\ZPanelVPS\ZPanel;

/**
 * zPanel VPS Metric Billing Provider for WHMCS
 * Supports: Disk Space, Bandwidth, CPU Cores, RAM, Snapshots
 * WHMCS Version: 7.9+ / 8.x / 9.x
 */
class MetricsProvider
{
    private array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Define the metrics available for products under this module.
     */
    public function metrics(): array
    {
        $metrics = [];

        if (class_exists('WHMCS\UsageBilling\Metrics\Metric')) {
            $unitGb = class_exists('WHMCS\UsageBilling\Metrics\Units\Gigabytes') ? new \WHMCS\UsageBilling\Metrics\Units\Gigabytes() : null;
            $unitWhole = class_exists('WHMCS\UsageBilling\Metrics\Units\WholeNumber') ? new \WHMCS\UsageBilling\Metrics\Units\WholeNumber() : null;
            $statType = defined('WHMCS\UsageBilling\Contracts\Metrics\MetricInterface::TYPE_STAT') ? \WHMCS\UsageBilling\Contracts\Metrics\MetricInterface::TYPE_STAT : 'stat';

            $disk = new \WHMCS\UsageBilling\Metrics\Metric('disk_space', 'Disk Space');
            $disk->setDescription('NVMe SSD Depolama Alanı Tüketimi (GB)');
            if ($unitGb) $disk->setUnit($unitGb);
            $disk->setType($statType);
            $metrics[] = $disk;

            $bw = new \WHMCS\UsageBilling\Metrics\Metric('bandwidth', 'Bandwidth');
            $bw->setDescription('Aylık Ağ Trafiği Kotası / Kullanımı (GB)');
            if ($unitGb) $bw->setUnit($unitGb);
            $bw->setType($statType);
            $metrics[] = $bw;

            $cpu = new \WHMCS\UsageBilling\Metrics\Metric('cpu_cores', 'CPU Cores');
            $cpu->setDescription('Tahsis Edilen vCPU Çekirdek Sayısı');
            if ($unitWhole) $cpu->setUnit($unitWhole);
            $cpu->setType($statType);
            $metrics[] = $cpu;

            $ram = new \WHMCS\UsageBilling\Metrics\Metric('ram_mb', 'RAM Memory (MB)');
            $ram->setDescription('Tahsis Edilen RAM Bellek (MB)');
            if ($unitWhole) $ram->setUnit($unitWhole);
            $ram->setType($statType);
            $metrics[] = $ram;

            $snapshots = new \WHMCS\UsageBilling\Metrics\Metric('snapshots', 'Snapshots / Backups');
            $snapshots->setDescription('Anlık Görüntü ve Bulut Yedek Adedi');
            if ($unitWhole) $snapshots->setUnit($unitWhole);
            $snapshots->setType($statType);
            $metrics[] = $snapshots;
        }

        return $metrics;
    }

    /**
     * Fetch usage data for all tenants on the server (WHMCS cron sync).
     */
    public function usage(): array
    {
        return [];
    }

    /**
     * Fetch usage data for a single specific tenant.
     */
    public function tenantUsage($tenant): array
    {
        return [];
    }
}
