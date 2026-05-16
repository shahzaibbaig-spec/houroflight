<?php

namespace App\Http\Controllers;

use App\Support\EResourceCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use ZipArchive;

class EResourceController extends Controller
{
    public function __construct(private readonly EResourceCatalog $catalog)
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $catalog = $this->catalog->getCatalog();
        $payload = $this->buildSearchPayload($request, $catalog);

        return view('e-resources.index', [
            'classFilters' => $catalog['classes'] ?? [],
            'subjectFilters' => $catalog['subjects'] ?? [],
            'resourceCount' => (int) ($catalog['resource_count'] ?? 0),
            'catalogGeneratedAt' => $catalog['generated_at'] ?? null,
            'initialPayload' => $payload,
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $catalog = $this->catalog->getCatalog();
        $payload = $this->buildSearchPayload($request, $catalog);

        return response()->json($payload);
    }

    public function download(Request $request, string $id)
    {
        $catalog = $this->catalog->getCatalog();
        /** @var array<string, mixed>|null $resource */
        $resource = collect($catalog['resources'] ?? [])->firstWhere('id', $id);

        if (! is_array($resource)) {
            abort(404, 'Resource not found.');
        }

        $zipRelativePath = (string) ($resource['zip_path'] ?? '');
        $entryPath = (string) ($resource['entry_path'] ?? '');
        if ($zipRelativePath === '' || $entryPath === '') {
            abort(404, 'Resource path is invalid.');
        }

        $zipPath = $this->catalog->bundlesRoot().DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $zipRelativePath);
        if (! is_file($zipPath)) {
            abort(404, 'Source bundle not found.');
        }

        $fileName = basename($entryPath);
        $extension = strtolower((string) pathinfo($fileName, PATHINFO_EXTENSION));
        $mimeType = $this->mimeTypeForExtension($extension);
        $disposition = $request->query('disposition') === 'inline' ? 'inline' : 'attachment';

        $headers = [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $this->contentDispositionHeader($disposition, $fileName),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ];

        return response()->stream(function () use ($zipPath, $entryPath) {
            $zip = new ZipArchive();
            if ($zip->open($zipPath) !== true) {
                return;
            }

            $stream = $zip->getStream($entryPath);
            if (! is_resource($stream)) {
                $zip->close();
                return;
            }

            while (! feof($stream)) {
                echo fread($stream, 8192);
            }

            fclose($stream);
            $zip->close();
        }, 200, $headers);
    }

    /**
     * @param array<string, mixed> $catalog
     * @return array<string, mixed>
     */
    private function buildSearchPayload(Request $request, array $catalog): array
    {
        $query = trim((string) $request->query('q', ''));
        $class = trim((string) $request->query('class', ''));
        $subject = trim((string) $request->query('subject', ''));
        $page = max(1, (int) $request->query('page', 1));
        $perPage = max(1, min(24, (int) $request->query('per_page', 12)));

        /** @var Collection<int, array<string, mixed>> $items */
        $items = collect($catalog['resources'] ?? []);

        if ($class !== '') {
            $items = $items->filter(function ($item) use ($class): bool {
                return strcasecmp((string) ($item['class'] ?? ''), $class) === 0;
            });
        }

        if ($subject !== '') {
            $items = $items->filter(function ($item) use ($subject): bool {
                return strcasecmp((string) ($item['subject'] ?? ''), $subject) === 0;
            });
        }

        if ($query !== '') {
            $needle = Str::lower($query);
            $items = $items->filter(function ($item) use ($needle): bool {
                $haystack = Str::lower(implode(' ', [
                    (string) ($item['title'] ?? ''),
                    (string) ($item['class'] ?? ''),
                    (string) ($item['subject'] ?? ''),
                    (string) ($item['source_folder'] ?? ''),
                    (string) ($item['source_bundle'] ?? ''),
                    (string) ($item['entry_path'] ?? ''),
                ]));

                return str_contains($haystack, $needle);
            });
        }

        $filtered = $items->values();
        $total = $filtered->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);
        $paged = $filtered->forPage($page, $perPage)->values()->all();

        return [
            'query' => [
                'q' => $query,
                'class' => $class,
                'subject' => $subject,
                'page' => $page,
                'per_page' => $perPage,
            ],
            'pagination' => [
                'total' => $total,
                'count' => count($paged),
                'current_page' => $page,
                'per_page' => $perPage,
                'last_page' => $lastPage,
                'has_prev' => $page > 1,
                'has_next' => $page < $lastPage,
            ],
            'items' => $paged,
        ];
    }

    private function contentDispositionHeader(string $disposition, string $fileName): string
    {
        $asciiFallback = preg_replace('/[^A-Za-z0-9\.\-\_ ]/', '_', $fileName) ?: 'resource-file';
        $encoded = rawurlencode($fileName);

        return sprintf(
            "%s; filename=\"%s\"; filename*=UTF-8''%s",
            $disposition,
            addcslashes($asciiFallback, "\"\\"),
            $encoded
        );
    }

    private function mimeTypeForExtension(string $extension): string
    {
        return match ($extension) {
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'rtf' => 'application/rtf',
            'txt' => 'text/plain',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => 'application/octet-stream',
        };
    }
}

