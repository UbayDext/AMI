<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ptk = App\Models\Ptk::latest()->first();

if (!$ptk) {
    echo "No PTK found.\n";
    exit;
}

echo "PTK ID: " . $ptk->id . "\n";
echo "Question ID: " . $ptk->question_id . "\n";
echo "Standard ID (PTK): " . ($ptk->standard_id ?? 'NULL') . "\n";
echo "Category: " . ($ptk->category ?? 'NULL') . "\n";

$question = App\Models\Question::find($ptk->question_id);
if ($question) {
    echo "Question Standard ID: " . ($question->standard_id ?? 'NULL') . "\n";
} else {
    echo "Question not found.\n";
}

$standard = App\Models\Standard::find($ptk->standard_id);
if ($standard) {
    echo "Standard Code: " . $standard->code . "\n";
} else {
    echo "Standard not found.\n";
}
