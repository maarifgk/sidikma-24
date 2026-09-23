<?php

require 'C:/laragon/www/yayasan-app/vendor/autoload.php';

$app = require 'C:/laragon/www/yayasan-app/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = Illuminate\Support\Facades\DB::connection();
$ids = [836, 837, 838, 839, 841, 842, 843];

$result = [
    'connection' => null,
    'schema' => [],
    'records' => [],
    'duplicate_legacy_invoice_id' => [],
];

$result['connection'] = $db->selectOne('SELECT current_database() AS database_name, current_user AS database_user');
$result['schema'] = $db->select("SELECT column_name, data_type, is_nullable FROM information_schema.columns WHERE table_schema = 'public' AND table_name = 'payment_invoices' ORDER BY ordinal_position");
$result['records'] = $db->table('payment_invoices')->whereIn('legacy_invoice_id', $ids)->orderBy('legacy_invoice_id')->get()->all();
$result['duplicate_legacy_invoice_id'] = $db->table('payment_invoices')
    ->select('legacy_invoice_id')
    ->whereIn('legacy_invoice_id', $ids)
    ->groupBy('legacy_invoice_id')
    ->havingRaw('COUNT(*) > 1')
    ->get()
    ->all();

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), PHP_EOL;
