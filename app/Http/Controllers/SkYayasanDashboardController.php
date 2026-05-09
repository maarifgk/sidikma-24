<?php

namespace App\Http\Controllers;

use App\Models\SkTemplate;
use App\Models\SkYayasanDocument;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class SkYayasanDashboardController extends Controller
{
    public function index()
    {
        $this->ensureRoleOne();

        $currentYear = (int) now()->format('Y');
        $templates = SkTemplate::query()->latest()->get();
        $classes = DB::table('kelas')->select('id', 'nama_kelas')->orderBy('nama_kelas')->get();
        $users = $this->usersQuery()->limit(300)->get();
        $recentDocuments = $this->documentsQuery()->limit(50)->get();

        return view('backend.sk_yayasan.dashboard', [
            'title' => 'SK Yayasan',
            'templates' => $templates,
            'classes' => $classes,
            'users' => $users,
            'documents' => $recentDocuments,
            'defaultYear' => $currentYear,
            'stats' => [
                'template_count' => $templates->count(),
                'document_count' => SkYayasanDocument::query()->count(),
                'document_count_current_year' => SkYayasanDocument::query()->where('tahun_sk', $currentYear)->count(),
                'user_count_with_document' => SkYayasanDocument::query()->distinct('user_id')->count('user_id'),
            ],
            'importResult' => session('sk_yayasan_import_result'),
        ]);
    }

    public function uploadSingle(Request $request)
    {
        $this->ensureRoleOne();

        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'tahun_sk' => 'required|integer|min:2000|max:2100',
            'sk_template_id' => 'nullable|integer|exists:sk_templates,id',
            'file_sk' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $user = $this->usersQuery()->where('users.id', (int) $validated['user_id'])->first();
        abort_if(!$user, 404);

        $this->storeDocumentForUser(
            $user,
            $request->file('file_sk'),
            (int) $validated['tahun_sk'],
            isset($validated['sk_template_id']) ? (int) $validated['sk_template_id'] : null,
            'single',
            'manual'
        );

        Alert::success('File SK berhasil diupload dan terhubung ke user.');

        return redirect()->route('sk-yayasan.index');
    }

    public function import(Request $request)
    {
        $this->ensureRoleOne();

        $validated = $request->validate([
            'tahun_sk' => 'required|integer|min:2000|max:2100',
            'sk_template_id' => 'nullable|integer|exists:sk_templates,id',
            'kelas_id' => 'nullable|integer|exists:kelas,id',
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $candidateUsers = $this->usersQuery()
            ->when(
                !empty($validated['kelas_id']),
                fn ($query) => $query->where('users.kelas_id', (int) $validated['kelas_id'])
            )
            ->get();

        $matchedCount = 0;
        $updatedCount = 0;
        $unmatchedFiles = [];
        $ambiguousFiles = [];

        foreach ($request->file('files', []) as $file) {
            [$matchedUser, $matchedBy, $reason] = $this->matchUploadedFileToUser($file, $candidateUsers);

            if (!$matchedUser) {
                if ($reason === 'ambiguous') {
                    $ambiguousFiles[] = $file->getClientOriginalName();
                } else {
                    $unmatchedFiles[] = $file->getClientOriginalName();
                }
                continue;
            }

            $result = $this->storeDocumentForUser(
                $matchedUser,
                $file,
                (int) $validated['tahun_sk'],
                isset($validated['sk_template_id']) ? (int) $validated['sk_template_id'] : null,
                'import',
                $matchedBy
            );

            if ($result === 'updated') {
                $updatedCount++;
            } else {
                $matchedCount++;
            }
        }

        $summary = [
            'matched_count' => $matchedCount,
            'updated_count' => $updatedCount,
            'unmatched_files' => $unmatchedFiles,
            'ambiguous_files' => $ambiguousFiles,
            'total_files' => count($request->file('files', [])),
        ];

        if (($matchedCount + $updatedCount) > 0) {
            Alert::success('Import SK selesai diproses.');
        } else {
            Alert::warning('Tidak ada file yang berhasil dicocokkan ke user.');
        }

        return redirect()->route('sk-yayasan.index')->with('sk_yayasan_import_result', $summary);
    }

    public function download(SkYayasanDocument $document)
    {
        $this->ensureRoleOne();

        abort_unless(Storage::disk('public')->exists($document->file_path), 404);

        return Storage::disk('public')->download($document->file_path, $document->original_filename);
    }

    protected function storeDocumentForUser($user, UploadedFile $file, int $year, ?int $templateId, string $sourceType, string $matchedBy): string
    {
        $existing = SkYayasanDocument::query()
            ->where('user_id', (int) $user->id)
            ->where('tahun_sk', $year)
            ->when(
                $templateId === null,
                fn ($query) => $query->whereNull('sk_template_id'),
                fn ($query) => $query->where('sk_template_id', $templateId)
            )
            ->first();

        if ($existing && Storage::disk('public')->exists($existing->file_path)) {
            Storage::disk('public')->delete($existing->file_path);
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        $storedFilename = Str::slug((string) ($user->nama_lengkap ?? 'user')) . '-' . $year . '-' . Str::random(8) . '.' . $extension;
        $relativePath = 'sk_yayasan/' . $year . '/' . $storedFilename;
        Storage::disk('public')->putFileAs('sk_yayasan/' . $year, $file, $storedFilename);

        $payload = [
            'user_id' => (int) $user->id,
            'sk_template_id' => $templateId,
            'tahun_sk' => $year,
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename' => $storedFilename,
            'file_path' => $relativePath,
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'source_type' => $sourceType,
            'matched_by' => $matchedBy,
            'uploaded_by' => (int) request()->user()->id,
        ];

        if ($existing) {
            $existing->update($payload);
            return 'updated';
        }

        SkYayasanDocument::query()->create($payload);
        return 'created';
    }

    protected function matchUploadedFileToUser(UploadedFile $file, Collection $users): array
    {
        $normalizedFilename = $this->normalizeMatchToken(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $matches = [];

        foreach ($users as $user) {
            foreach ($this->userMatchTokens($user) as $candidate) {
                if ($candidate['token'] === '' || !str_contains($normalizedFilename, $candidate['token'])) {
                    continue;
                }

                $matches[] = [
                    'user' => $user,
                    'field' => $candidate['field'],
                    'score' => $candidate['score'] + strlen($candidate['token']),
                ];
            }
        }

        if (empty($matches)) {
            return [null, null, 'not_found'];
        }

        usort($matches, fn ($left, $right) => $right['score'] <=> $left['score']);
        $best = $matches[0];
        $topMatches = array_values(array_filter($matches, fn ($item) => $item['score'] === $best['score']));
        $uniqueUserIds = collect($topMatches)->pluck('user.id')->unique()->values();

        if ($uniqueUserIds->count() > 1) {
            return [null, null, 'ambiguous'];
        }

        return [$best['user'], $best['field'], 'matched'];
    }

    protected function userMatchTokens($user): array
    {
        $candidates = [];

        $fullName = $this->normalizeMatchToken((string) ($user->nama_lengkap ?? ''));
        if (strlen($fullName) >= 6) {
            $candidates[] = ['field' => 'nama_user', 'token' => $fullName, 'score' => 900];
        }

        $nameParts = preg_split('/\s+/', strtolower(Str::ascii((string) ($user->nama_lengkap ?? '')))) ?: [];
        $nameParts = array_values(array_filter(array_map(function ($part) {
            return preg_replace('/[^a-z0-9]/', '', $part) ?: '';
        }, $nameParts), fn ($part) => strlen($part) >= 4));

        foreach ($nameParts as $part) {
            $candidates[] = ['field' => 'nama_user', 'token' => $part, 'score' => 180];
        }

        for ($index = 0; $index < count($nameParts) - 1; $index++) {
            $pair = $nameParts[$index] . $nameParts[$index + 1];
            if (strlen($pair) >= 8) {
                $candidates[] = ['field' => 'nama_user', 'token' => $pair, 'score' => 420];
            }
        }

        $ewanugk = $this->normalizeMatchToken((string) ($user->nis ?? ''));
        if (strlen($ewanugk) >= 4) {
            $candidates[] = ['field' => 'ewanugk', 'token' => $ewanugk, 'score' => 1000];
        }

        $nuptk = $this->normalizeMatchToken((string) ($user->nuptk ?? ''));
        if (strlen($nuptk) >= 4) {
            $candidates[] = ['field' => 'nuptk', 'token' => $nuptk, 'score' => 700];
        }

        $nip = $this->normalizeMatchToken((string) ($user->nip ?? ''));
        if (strlen($nip) >= 4) {
            $candidates[] = ['field' => 'nip', 'token' => $nip, 'score' => 700];
        }

        $uniqueCandidates = [];
        foreach ($candidates as $candidate) {
            $key = $candidate['field'] . ':' . $candidate['token'];
            if (!isset($uniqueCandidates[$key]) || $candidate['score'] > $uniqueCandidates[$key]['score']) {
                $uniqueCandidates[$key] = $candidate;
            }
        }

        return array_values($uniqueCandidates);
    }

    protected function normalizeMatchToken(string $value): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower(Str::ascii($value))) ?: '';
    }

    protected function usersQuery()
    {
        return DB::table('users')
            ->select(
                'users.id',
                'users.nama_lengkap',
                'users.nis',
                'users.nuptk',
                'users.nip',
                'users.periode',
                'users.kelas_id',
                'kelas.nama_kelas',
                'periode.nama_periode'
            )
            ->leftJoin('kelas', 'kelas.id', '=', 'users.kelas_id')
            ->leftJoin('periode', 'periode.id', '=', 'users.periode')
            ->whereIn('users.role', [2, 3])
            ->where(function ($query) {
                $query->whereNull('users.status')->orWhere('users.status', '!=', 'Lulus');
            })
            ->orderBy('users.nama_lengkap');
    }

    protected function documentsQuery()
    {
        return DB::table('sk_yayasan_documents as docs')
            ->select(
                'docs.*',
                'users.nama_lengkap',
                'kelas.nama_kelas',
                'templates.name as template_name'
            )
            ->join('users', 'users.id', '=', 'docs.user_id')
            ->leftJoin('kelas', 'kelas.id', '=', 'users.kelas_id')
            ->leftJoin('sk_templates as templates', 'templates.id', '=', 'docs.sk_template_id')
            ->orderByDesc('docs.updated_at');
    }

    protected function ensureRoleOne(): void
    {
        abort_unless(request()->user() && (int) request()->user()->role === 1, 403);
    }
}
