<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

class EResourceCatalog
{
    private const BUNDLES_DIR = 'e-resources/bundles/Mega Bundle Of Work Sheets & Books';
    private const CATALOG_FILE = 'e-resources/catalog.json';

    /**
     * Only include formats that users can practically download/print.
     *
     * @var array<int, string>
     */
    private const ALLOWED_EXTENSIONS = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        'txt', 'rtf', 'jpg', 'jpeg', 'png', 'webp',
    ];

    /**
     * @return array<string, mixed>
     */
    public function getCatalog(bool $refresh = false): array
    {
        $catalogPath = storage_path('app/'.self::CATALOG_FILE);

        if (! $refresh && File::exists($catalogPath)) {
            $decoded = json_decode((string) File::get($catalogPath), true);
            if (is_array($decoded) && isset($decoded['resources']) && is_array($decoded['resources'])) {
                return $decoded;
            }
        }

        $catalog = $this->buildCatalog();
        File::ensureDirectoryExists(dirname($catalogPath));
        File::put($catalogPath, json_encode($catalog, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $catalog;
    }

    public function bundlesRoot(): string
    {
        return storage_path('app/'.self::BUNDLES_DIR);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCatalog(): array
    {
        $bundlesRoot = $this->bundlesRoot();
        if (! is_dir($bundlesRoot)) {
            return [
                'generated_at' => now()->toIso8601String(),
                'resource_count' => 0,
                'classes' => [],
                'subjects' => [],
                'resources' => [],
                'error' => 'Bundles directory not found at '.$bundlesRoot,
            ];
        }

        $zipFiles = collect(File::allFiles($bundlesRoot))
            ->filter(function ($file): bool {
                return strtolower($file->getExtension()) === 'zip';
            })
            ->values();

        $resources = [];

        foreach ($zipFiles as $zipFile) {
            $zipPath = $zipFile->getRealPath();
            if (! is_string($zipPath) || $zipPath === '') {
                continue;
            }

            $zip = new ZipArchive();
            if ($zip->open($zipPath) !== true) {
                continue;
            }

            $zipRelativePath = str_replace('\\', '/', $zipFile->getRelativePathname());
            $zipLabel = $this->normalizeBundleLabel($zipFile->getBasename('.zip'));

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entryPath = $zip->getNameIndex($i);
                if (! is_string($entryPath) || $entryPath === '' || str_ends_with($entryPath, '/')) {
                    continue;
                }

                $normalizedPath = str_replace('\\', '/', $entryPath);
                $extension = strtolower(pathinfo($normalizedPath, PATHINFO_EXTENSION));
                if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
                    continue;
                }

                $segments = array_values(array_filter(
                    array_map('trim', explode('/', $normalizedPath)),
                    fn ($segment) => $segment !== ''
                ));

                $fileName = basename($normalizedPath);
                $title = $this->cleanupLabel(pathinfo($fileName, PATHINFO_FILENAME));
                $classLabel = $this->detectClass($zipLabel, $segments, $normalizedPath);
                $subjectLabel = $this->detectSubject($zipLabel, $segments, $normalizedPath);

                $stat = $zip->statIndex($i);
                $sizeBytes = is_array($stat) ? (int) ($stat['size'] ?? 0) : 0;
                $folderHint = count($segments) > 1 ? implode(' / ', array_slice($segments, 0, -1)) : '';

                $resources[] = [
                    'id' => hash('sha256', $zipRelativePath.'|'.$normalizedPath),
                    'title' => $title !== '' ? $title : $fileName,
                    'class' => $classLabel,
                    'subject' => $subjectLabel,
                    'extension' => $extension,
                    'size_bytes' => $sizeBytes,
                    'size_human' => $this->humanFileSize($sizeBytes),
                    'zip_path' => $zipRelativePath,
                    'entry_path' => $normalizedPath,
                    'source_folder' => $folderHint,
                    'source_bundle' => $zipLabel,
                ];
            }

            $zip->close();
        }

        usort($resources, function (array $a, array $b): int {
            return [$a['class'], $a['subject'], $a['title']] <=> [$b['class'], $b['subject'], $b['title']];
        });

        $classes = collect($resources)->pluck('class')->filter()->unique()->sort()->values()->all();
        $subjects = collect($resources)->pluck('subject')->filter()->unique()->sort()->values()->all();

        return [
            'generated_at' => now()->toIso8601String(),
            'resource_count' => count($resources),
            'classes' => $classes,
            'subjects' => $subjects,
            'resources' => $resources,
        ];
    }

    /**
     * @param array<int, string> $segments
     */
    private function detectClass(string $zipLabel, array $segments, string $entryPath): string
    {
        $firstSegment = (string) ($segments[0] ?? '');
        $fromSegment = $this->classFromText($firstSegment);
        if ($fromSegment !== null) {
            return $fromSegment;
        }

        $fromZip = $this->classFromText($zipLabel);
        if ($fromZip !== null) {
            return $fromZip;
        }

        $haystack = strtolower($zipLabel.' '.implode(' ', $segments).' '.$entryPath);

        if (preg_match('/\bgrade\s*([1-9]|1[0-2])\b/i', $haystack, $match) === 1) {
            return 'Grade '.$match[1];
        }

        if (preg_match('/\bclass\s*([1-9]|1[0-2])\b/i', $haystack, $match) === 1) {
            return 'Class '.$match[1];
        }

        return 'General';
    }

    /**
     * @param array<int, string> $segments
     */
    private function detectSubject(string $zipLabel, array $segments, string $entryPath): string
    {
        $candidate = '';
        if (count($segments) >= 2) {
            $candidate = (string) $segments[count($segments) - 2];
        }

        $subject = $this->keywordSubject($candidate);
        if ($subject !== null) {
            return $subject;
        }

        if ($candidate !== '' && ! $this->looksLikeClassText($candidate)) {
            return $this->cleanupLabel($candidate);
        }

        $subject = $this->keywordSubject($entryPath);
        if ($subject !== null) {
            return $subject;
        }

        $subject = $this->keywordSubject($zipLabel);
        if ($subject !== null) {
            return $subject;
        }

        return 'General';
    }

    private function keywordSubject(string $value): ?string
    {
        $v = strtolower($value);

        if (str_contains($v, 'english')) {
            return 'English';
        }
        if (str_contains($v, 'math') || str_contains($v, 'maths') || str_contains($v, 'mathematics')) {
            return 'Math';
        }
        if (str_contains($v, 'general science')) {
            return 'General Science';
        }
        if (str_contains($v, 'science')) {
            return 'Science';
        }
        if (str_contains($v, 'social studies')) {
            return 'Social Studies';
        }
        if (str_contains($v, 'gk') || str_contains($v, 'general knowledge')) {
            return 'General Knowledge';
        }
        if (str_contains($v, 'urdu')) {
            return 'Urdu';
        }
        if (str_contains($v, 'sindhi')) {
            return 'Sindhi';
        }
        if (str_contains($v, 'computer')) {
            return 'Computer';
        }
        if (str_contains($v, 'islamiat') || str_contains($v, 'islamic') || str_contains($v, 'qaida') || str_contains($v, 'namaz')) {
            return 'Islamic Studies';
        }
        if (str_contains($v, 'grammar')) {
            return 'Grammar';
        }
        if (str_contains($v, 'drawing') || str_contains($v, 'coloring') || str_contains($v, 'colouring') || str_contains($v, 'visual-arts')) {
            return 'Art & Drawing';
        }
        if (str_contains($v, 'hand writing') || str_contains($v, 'handwriting') || str_contains($v, 'cursive')) {
            return 'Handwriting';
        }
        if (str_contains($v, 'assessment')) {
            return 'Assessment';
        }
        if (str_contains($v, 'story')) {
            return 'Stories';
        }
        if (str_contains($v, 'summer')) {
            return 'Summer Pack';
        }

        return null;
    }

    private function normalizeBundleLabel(string $value): string
    {
        $clean = preg_replace('/-?\d{8}T\d{6}Z-\d+-\d+$/', '', $value) ?? $value;
        $clean = preg_replace('/\s+\d{8}T\d{6}Z\s+\d+\s+\d+$/', '', $clean) ?? $clean;

        return $this->cleanupLabel($clean);
    }

    private function classFromText(string $value): ?string
    {
        $v = strtolower($value);

        if (preg_match('/\bgrade\s*([1-9]|1[0-2])\b/i', $v, $match) === 1) {
            return 'Grade '.$match[1];
        }

        if (preg_match('/\bclass\s*([1-9]|1[0-2])\b/i', $v, $match) === 1) {
            return 'Class '.$match[1];
        }

        if (str_contains($v, 'prep') || str_contains($v, 'kg ii')) {
            return 'Prep / KG II';
        }

        if (str_contains($v, 'play group') || str_contains($v, 'preschool') || str_contains($v, 'pre school')) {
            return 'Play Group / Preschool';
        }

        if (str_contains($v, 'kg and nursery')) {
            return 'KG & Nursery';
        }

        if (str_contains($v, 'nursery')) {
            return 'Nursery';
        }

        if (preg_match('/\bkg\b/i', $v) === 1 || str_contains($v, 'kindergarten')) {
            return 'KG';
        }

        if (str_contains($v, 'summer pack')) {
            return 'Summer Pack';
        }

        if (str_contains($v, 'assessment')) {
            return 'Assessment';
        }

        return null;
    }

    private function looksLikeClassText(string $value): bool
    {
        $v = strtolower($value);

        return preg_match('/\b(grade|class)\s*[0-9]+/i', $v) === 1
            || str_contains($v, 'kg')
            || str_contains($v, 'nursery')
            || str_contains($v, 'prep')
            || str_contains($v, 'play group')
            || str_contains($v, 'preschool');
    }

    private function cleanupLabel(string $value): string
    {
        $decoded = str_replace(['_', '-'], ' ', $value);
        $decoded = preg_replace('/\s+/', ' ', $decoded) ?? $decoded;
        $decoded = trim($decoded);
        if ($decoded === '') {
            return '';
        }

        // Keep all-caps short tokens (e.g., KG, PDF) stable while title-casing normal words.
        return Str::of($decoded)->title()->toString();
    }

    private function humanFileSize(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $pow = min((int) floor(log($bytes, 1024)), count($units) - 1);
        $value = $bytes / (1024 ** $pow);

        return number_format($value, $pow === 0 ? 0 : 1).' '.$units[$pow];
    }
}
