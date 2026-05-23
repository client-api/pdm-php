# pdm-php

PHP SDK for the Proxmox Datacenter Manager API. Generated from
the upstream `apidoc.js` from Proxmox Datacenter Manager via [openapi-generator-cli][gen] with custom
Mustache template overrides.

> **Not an official Proxmox project.** Community SDK derived from the
> upstream `apidoc.js`. Always verify against the upstream API viewer.
> <https://pdm.proxmox.com/>.

Requires PHP ≥ 7.4.

## Install

```bash
composer require client-api/pdm
```

## Usage

```php
<?php
require 'vendor/autoload.php';

use ClientApi\Pdm\Configuration;
use ClientApi\Pdm\Pve;

$cfg = Configuration::getDefaultConfiguration()
    ->setHost('https://pdm1.example.com:8443/api2/json')
    ->setApiKey('Authorization', 'PDMAPIToken=user@realm!tokenid=uuid-secret');

$pve = new Pdm($cfg);

// Per-tag accessors are lazily instantiated and share the same Configuration.
$status = $pdm->qemu()->qemuVmStatus(node: 'pdm1', vmid: 100);
$nodes  = $pdm->nodes()->nodesGetNodes();
```

The unified `Pdm` class wraps each per-tag API class (`QemuApi`,
`LxcApi`, `ClusterApi`, `NodesApi`, …) so consumers don't need to
instantiate them individually.

## Compound configs

PVE encodes many fields as CLI-style shorthand strings
(`net0=virtio,bridge=vmbr0,firewall=1`). Round-trip helpers are
emitted for every compound config schema:

```php
use ClientApi\Pdm\Model\PveQemuNetConfig;

$cfg = new PveQemuNetConfig([
    'model'    => 'virtio',
    'bridge'   => 'vmbr0',
    'firewall' => 1,
]);
$shorthand = $cfg->toShorthand();
// → 'virtio,bridge=vmbr0,firewall=1'

$parsed = PveQemuNetConfig::fromShorthand($shorthand);
```

## Indexed families

Numbered properties (`net0..net31`, `mp0..mp255`, …) are exposed on
every model as a single collapsed `getNets()` / `setNets()` accessor.
The per-index `getNet0`/`setNet0`/… methods are filtered out of the
class surface (the wire format is preserved internally via a `__call`
magic dispatcher):

```php
$req->setNets([
    0 => 'virtio,bridge=vmbr0',
    3 => 'e1000,bridge=vmbr1',
]);
// Wire format: { "net0": "virtio,bridge=vmbr0", "net3": "e1000,bridge=vmbr1" }
```

## License

Apache 2.0 — see [LICENSE](./LICENSE).

[gen]: https://openapi-generator.tech
