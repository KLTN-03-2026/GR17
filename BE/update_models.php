<?php
$dir = __DIR__ . '/app/Models';
$files = glob($dir . '/*.php');
foreach ($files as $file) {
    if (basename($file) == 'User.php' || basename($file) == 'new.php') continue;
    $c = file_get_contents($file);
    if (strpos($c, 'GeneratesIdFromZero') === false) {
        $c = preg_replace('/(class\s+[A-Za-z0-9_]+\s*(?:extends\s+[A-Za-z0-9_\\\\]+\s*)?(?:implements\s+[A-Za-z0-9_\\\\\s,]+\s*)?\{)/', "$1\n    use \\App\\Traits\\GeneratesIdFromZero;", $c);
        file_put_contents($file, $c);
        echo 'Updated: ' . basename($file) . PHP_EOL;
    }
}
echo "Done updating models.\n";
