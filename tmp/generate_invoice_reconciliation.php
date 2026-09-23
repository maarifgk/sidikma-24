<?php

require 'C:/laragon/www/yayasan-app/vendor/autoload.php';

$app = require 'C:/laragon/www/yayasan-app/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$previewPath = 'C:/laragon/www/yayasan-app/storage/app/audits/sidikma-finance/20260920_064500-invoice-preview/invoice-migration-preview.json';
$ids = [836, 837, 838, 839, 841, 842, 843];
$source = collect([
    ['legacy_invoice_id'=>836,'target_invoice_number'=>'SIDIKMA-TAGIHAN-836','user_id'=>1694,'employee_id'=>null,'school_id'=>54,'academic_year'=>'2023/2024','payment_type'=>'Pembayaran Batik','amount'=>1774500,'mapped_status'=>'unpaid'],
    ['legacy_invoice_id'=>837,'target_invoice_number'=>'SIDIKMA-TAGIHAN-837','user_id'=>1693,'employee_id'=>null,'school_id'=>53,'academic_year'=>'2024/2025','payment_type'=>'Pembayaran Batik','amount'=>1023750,'mapped_status'=>'unpaid'],
    ['legacy_invoice_id'=>838,'target_invoice_number'=>'SIDIKMA-TAGIHAN-838','user_id'=>1805,'employee_id'=>613,'school_id'=>61,'academic_year'=>'2025/2026','payment_type'=>'Pembayaran SK','amount'=>50000,'mapped_status'=>'unpaid'],
    ['legacy_invoice_id'=>839,'target_invoice_number'=>'SIDIKMA-TAGIHAN-839','user_id'=>603,'employee_id'=>530,'school_id'=>29,'academic_year'=>'2025/2026','payment_type'=>'Pembayaran SK','amount'=>50000,'mapped_status'=>'unpaid'],
    ['legacy_invoice_id'=>841,'target_invoice_number'=>'SIDIKMA-TAGIHAN-841','user_id'=>1826,'employee_id'=>634,'school_id'=>61,'academic_year'=>'2025/2026','payment_type'=>'Pembayaran SK','amount'=>75000,'mapped_status'=>'unpaid'],
    ['legacy_invoice_id'=>842,'target_invoice_number'=>'SIDIKMA-TAGIHAN-842','user_id'=>1825,'employee_id'=>633,'school_id'=>61,'academic_year'=>'2025/2026','payment_type'=>'Pembayaran SK','amount'=>75000,'mapped_status'=>'unpaid'],
    ['legacy_invoice_id'=>843,'target_invoice_number'=>'SIDIKMA-TAGIHAN-843','user_id'=>1827,'employee_id'=>635,'school_id'=>61,'academic_year'=>'2023/2024','payment_type'=>'Pembayaran SK','amount'=>125000,'mapped_status'=>'paid'],
])->keyBy('legacy_invoice_id');
$target = Illuminate\Support\Facades\DB::table('payment_invoices')->whereIn('legacy_invoice_id', $ids)->get()->keyBy('legacy_invoice_id');

$fields = ['legacy_invoice_id', 'invoice_number', 'user_id', 'employee_id', 'school_id', 'academic_year', 'payment_type', 'amount', 'status', 'paid_at', 'description', 'notes'];
$rows = [];

foreach ($ids as $id) {
    $s = $source->get($id);
    $t = $target->get($id);
    if (!$s && !$t) { $rows[] = ['legacy_invoice_id'=>$id, 'classification'=>'source_missing', 'decision'=>'BLOCKED']; continue; }
    if (!$t) { $rows[] = ['legacy_invoice_id'=>$id, 'classification'=>'target_missing', 'decision'=>'ELIGIBLE_FOR_NEW_MIGRATION']; continue; }
    if (!$s) { $rows[] = ['legacy_invoice_id'=>$id, 'classification'=>'source_missing', 'decision'=>'BLOCKED']; continue; }

    $comparisons = [
        'legacy_invoice_id' => [$s['legacy_invoice_id'], $t->legacy_invoice_id],
        'invoice_number' => [$s['target_invoice_number'], $t->invoice_number],
        'user_id' => [$s['user_id'], $t->user_id],
        'employee_id' => [$s['employee_id'], $t->employee_id],
        'school_id' => [$s['school_id'], $t->school_id],
        'academic_year' => [$s['academic_year'], $t->academic_year],
        'payment_type' => [$s['payment_type'], $t->notes],
        'amount' => [(float)$s['amount'], (float)$t->amount],
        'status' => [$s['mapped_status'], $t->status],
        'paid_at' => ['not_available_in_preview', $t->paid_at],
        'description' => ['not_available_in_preview', $t->description],
        'notes' => ['not_available_in_preview', $t->notes],
    ];
    $diffs = [];
    foreach ($comparisons as $field => [$sv, $tv]) {
        if ($sv === 'not_available_in_preview') { $diffs[$field] = ['source'=>$sv, 'target'=>$tv, 'classification'=>'requires_business_decision']; continue; }
        $same = ((string)$sv === (string)$tv) || (is_numeric($sv) && is_numeric($tv) && (float)$sv === (float)$tv);
        if ($field === 'payment_type' && is_string($tv)) {
            $same = str_contains(strtolower($tv), strtolower((string)$sv));
        }
        $diffs[$field] = ['source'=>$sv, 'target'=>$tv, 'classification'=>$same ? 'identical' : 'major_difference'];
    }
    $hardDiff = collect($diffs)->contains(fn($d) => $d['classification'] === 'major_difference');
    $missingSourceFields = collect($diffs)->contains(fn($d) => $d['classification'] === 'requires_business_decision');
    $classification = $hardDiff ? 'major_difference' : ($missingSourceFields ? 'requires_business_decision' : 'identical');
    $decision = $hardDiff ? 'BLOCKED' : ($missingSourceFields ? 'REQUIRES_BUSINESS_DECISION' : 'ACCEPT_EXISTING');
    $rows[] = ['legacy_invoice_id'=>$id, 'classification'=>$classification, 'decision'=>$decision, 'source'=>$s, 'target'=>(array)$t, 'comparisons'=>$diffs];
}

$stamp = date('Ymd_His');
$dir = 'C:/laragon/www/yayasan-app/storage/app/audits/sidikma-finance/'.$stamp.'-invoice-reconciliation';
mkdir($dir, 0777, true);
$summary = ['generated_at'=>date(DATE_ATOM), 'source_report'=>$previewPath, 'ids'=>$ids, 'count'=>count($rows), 'classifications'=>collect($rows)->countBy('classification'), 'decisions'=>collect($rows)->countBy('decision'), 'rows'=>$rows];
file_put_contents($dir.'/invoice-reconciliation.json', json_encode($summary, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
$fp = fopen($dir.'/invoice-reconciliation.csv', 'wb'); fputcsv($fp, ['legacy_invoice_id','classification','decision']); foreach ($rows as $r) fputcsv($fp, [$r['legacy_invoice_id'], $r['classification'], $r['decision']]); fclose($fp);
$md = "# Invoice Reconciliation\n\nGenerated: {$summary['generated_at']}\n\n"; $md .= "| ID | Classification | Decision |\n|---:|---|---|\n"; foreach ($rows as $r) $md .= "| {$r['legacy_invoice_id']} | {$r['classification']} | {$r['decision']} |\n"; $md .= "\nNo database writes were performed. Source fields unavailable in the preview are marked requires_business_decision.\n"; file_put_contents($dir.'/invoice-reconciliation-summary.md', $md);
echo json_encode(['report_dir'=>$dir, 'summary'=>$summary['classifications'], 'decisions'=>$summary['decisions']], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES), PHP_EOL;
