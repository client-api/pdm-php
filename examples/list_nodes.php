<?php
/**
 * Example: list cluster nodes.
 *
 * Run with:
 *
 *   PDM_HOST=https://pdm.example.com:8443 \
 *   PDM_TOKEN='PDMAPIToken=root@pam!auto=...' \
 *   php examples/list_nodes.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use ClientApi\Pdm\Configuration;
use ClientApi\Pdm\Pve;

$host = getenv('PDM_HOST') ?: 'https://localhost:8443';
$config = (new Configuration())
    ->setHost($host . '/api2/json')
    ->setApiKey('Authorization', getenv('PDM_TOKEN') ?: '');

$pve = new Pdm($config);
$response = $pdm->nodes()->nodesGetNodes();
$nodes = $response->getData() ?? [];

printf("Found %d node(s):\n", count($nodes));
foreach ($nodes as $node) {
    printf(
        "  - %s (status=%s, cpu=%s, mem=%s/%s)\n",
        $node->getNode() ?? '?',
        $node->getStatus() ?? '?',
        $node->getCpu() ?? '?',
        $node->getMem() ?? '?',
        $node->getMaxmem() ?? '?',
    );
}
