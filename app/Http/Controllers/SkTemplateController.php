<?php

namespace App\Http\Controllers;

use App\Models\SkTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class SkTemplateController extends Controller
{
    public function index()
    {
        $this->ensureRoleOne();

        return view('backend.sk_templates.index', [
            'title' => 'Template SK',
            'templates' => SkTemplate::query()->latest()->get(),
        ]);
    }

    public function create()
    {
        $this->ensureRoleOne();

        return view('backend.sk_templates.form', [
            'title' => 'Buat Template SK',
            'template' => new SkTemplate([
                'paper_size' => 'A4',
                'orientation' => 'portrait',
                'document_title' => 'SK Yayasan - {{nama_lengkap}}',
                'content' => $this->defaultContent(),
                'custom_css' => $this->defaultCss(),
                'is_active' => true,
            ]),
            'placeholders' => $this->placeholderDefinitions(),
            'previewSamples' => $this->previewSamples(),
            'formAction' => route('sk-templates.store'),
            'submitLabel' => 'Simpan Template',
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureRoleOne();

        $payload = $this->validatedPayload($request);
        $payload['slug'] = $this->makeUniqueSlug($payload['name']);
        $payload['is_active'] = $request->boolean('is_active');

        SkTemplate::create($payload);

        Alert::success('Template SK berhasil ditambahkan');

        return redirect()->route('sk-templates.index');
    }

    public function show(SkTemplate $skTemplate, Request $request)
    {
        $this->ensureRoleOne();

        $selectedUserId = $request->integer('user_id');
        $selectedUser = $selectedUserId ? $this->findUserOrFail($selectedUserId) : null;
        $renderedHtml = $selectedUser ? $this->renderTemplate($skTemplate, $selectedUser) : null;

        return view('backend.sk_templates.show', [
            'title' => 'Generate PDF SK',
            'template' => $skTemplate,
            'users' => $this->templateUsers(),
            'selectedUser' => $selectedUser,
            'renderedHtml' => $renderedHtml,
            'placeholders' => $this->placeholderDefinitions(),
        ]);
    }

    public function edit(SkTemplate $skTemplate)
    {
        $this->ensureRoleOne();

        return view('backend.sk_templates.form', [
            'title' => 'Edit Template SK',
            'template' => $skTemplate,
            'placeholders' => $this->placeholderDefinitions(),
            'previewSamples' => $this->previewSamples(),
            'formAction' => route('sk-templates.update', $skTemplate),
            'submitLabel' => 'Update Template',
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, SkTemplate $skTemplate)
    {
        $this->ensureRoleOne();

        $payload = $this->validatedPayload($request);
        $payload['slug'] = $this->makeUniqueSlug($payload['name'], $skTemplate->id);
        $payload['is_active'] = $request->boolean('is_active');

        $skTemplate->update($payload);

        Alert::success('Template SK berhasil diperbarui');

        return redirect()->route('sk-templates.index');
    }

    public function delete(SkTemplate $skTemplate)
    {
        $this->ensureRoleOne();

        $skTemplate->delete();

        Alert::success('Template SK berhasil dihapus');

        return redirect()->route('sk-templates.index');
    }

    public function preview(SkTemplate $skTemplate, $userId)
    {
        $this->ensureRoleOne();

        $user = $this->findUserOrFail($userId);

        return view('backend.sk_templates.preview', [
            'title' => $this->renderText($skTemplate->document_title, $user),
            'html' => $this->renderTemplate($skTemplate, $user),
            'customCss' => $skTemplate->custom_css,
        ]);
    }

    public function pdf(SkTemplate $skTemplate, $userId)
    {
        $this->ensureRoleOne();

        $user = $this->findUserOrFail($userId);
        $documentTitle = $this->renderText($skTemplate->document_title, $user);

        $pdf = Pdf::loadView('backend.sk_templates.pdf', [
            'title' => $documentTitle,
            'html' => $this->renderTemplate($skTemplate, $user),
            'customCss' => $skTemplate->custom_css,
        ])->setPaper($skTemplate->paper_size ?: 'A4', $skTemplate->orientation === 'landscape' ? 'landscape' : 'portrait');

        return $pdf->stream(Str::slug($documentTitle ?: $skTemplate->name ?: 'template-sk') . '.pdf');
    }

    protected function ensureRoleOne(): void
    {
        abort_unless(request()->user() && (int) request()->user()->role === 1, 403);
    }

    protected function validatedPayload(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'document_title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'paper_size' => 'required|in:A4,legal,letter',
            'orientation' => 'required|in:portrait,landscape',
            'custom_css' => 'nullable|string',
            'content' => 'required|string',
        ]);
    }

    protected function makeUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name) ?: 'template-sk';
        $slug = $baseSlug;
        $suffix = 1;

        while (
            SkTemplate::query()
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $suffix++;
            $slug = $baseSlug . '-' . $suffix;
        }

        return $slug;
    }

    protected function templateUsers()
    {
        return $this->usersQuery()->get();
    }

    protected function findUserOrFail(int $userId)
    {
        return $this->usersQuery()->where('users.id', $userId)->firstOrFail();
    }

    protected function usersQuery()
    {
        return DB::table('users')
            ->select(
                'users.*',
                'kelas.nama_kelas',
                'jurusan.nama_jurusan',
                'ketugasan.ketugasan as nama_ketugasan',
                'periode.nama_periode'
            )
            ->leftJoin('kelas', 'kelas.id', '=', 'users.kelas_id')
            ->leftJoin('jurusan', 'jurusan.id', '=', 'users.jurusan_id')
            ->leftJoin('ketugasan', 'ketugasan.id', '=', 'users.ketugasan')
            ->leftJoin('periode', 'periode.id', '=', 'users.periode')
            ->whereIn('users.role', [2, 3])
            ->where(function ($query) {
                $query->whereNull('users.status')->orWhere('users.status', '!=', 'Lulus');
            })
            ->orderBy('users.nama_lengkap');
    }

    protected function placeholderDefinitions(): array
    {
        return [
            ['key' => 'nama_lengkap', 'label' => 'Nama lengkap user'],
            ['key' => 'email', 'label' => 'Email user'],
            ['key' => 'nis', 'label' => 'EWanuGK / NPSN / NIS'],
            ['key' => 'nuptk', 'label' => 'NUPTK / NPK'],
            ['key' => 'nip', 'label' => 'NIP'],
            ['key' => 'tempat_lahir', 'label' => 'Tempat lahir'],
            ['key' => 'tgl_lahir', 'label' => 'Tanggal lahir terformat Indonesia'],
            ['key' => 'tmt', 'label' => 'TMT terformat Indonesia'],
            ['key' => 'alamat', 'label' => 'Alamat text biasa'],
            ['key' => 'alamat_html', 'label' => 'Alamat dengan line break HTML'],
            ['key' => 'nama_kelas', 'label' => 'Nama madrasah / sekolah'],
            ['key' => 'nama_jurusan', 'label' => 'Status kepegawaian'],
            ['key' => 'ketugasan', 'label' => 'Nama ketugasan'],
            ['key' => 'periode_sk', 'label' => 'Periode SK yayasan'],
            ['key' => 'ptt_lulus', 'label' => 'Pendidikan terakhir dan tahun lulus'],
            ['key' => 'p_studi', 'label' => 'Program studi'],
            ['key' => 'tanggal_cetak', 'label' => 'Tanggal cetak dokumen'],
            ['key' => 'waktu_cetak', 'label' => 'Tanggal dan jam cetak'],
            ['key' => 'tahun', 'label' => 'Tahun sekarang'],
            ['key' => 'bulan', 'label' => 'Nama bulan sekarang'],
        ];
    }

    protected function previewSamples(): array
    {
        return [
            'nama_lengkap' => 'Ahmad Fauzi, S.Pd.I',
            'email' => 'ahmad.fauzi@example.com',
            'nis' => 'EWG-2026-001',
            'nuptk' => '1234567890123456',
            'nip' => '197812312006041001',
            'tempat_lahir' => 'Gunungkidul',
            'tgl_lahir' => '12 Januari 1987',
            'tmt' => '01 Juli 2024',
            'alamat' => 'Jl. Pendidikan No. 12, Gunungkidul',
            'alamat_html' => 'Jl. Pendidikan No. 12<br>Gunungkidul',
            'nama_kelas' => 'MI YAPPI Contoh',
            'nama_jurusan' => 'Guru Tetap Yayasan',
            'ketugasan' => 'Mengajar Guru Kelas',
            'periode_sk' => 'Juli',
            'ptt_lulus' => 'S1 Pendidikan Islam, 2010',
            'p_studi' => 'Pendidikan Guru Madrasah Ibtidaiyah',
            'tanggal_cetak' => now()->translatedFormat('d F Y'),
            'waktu_cetak' => now()->translatedFormat('d F Y H:i'),
            'tahun' => now()->format('Y'),
            'bulan' => now()->translatedFormat('F'),
        ];
    }

    protected function defaultContent(): string
    {
        return <<<'HTML'
<div class="document">
    <div class="text-center">
        <h2>SURAT KEPUTUSAN YAYASAN</h2>
        <p>Nomor: ........................................</p>
    </div>

    <p>Yang bertanda tangan di bawah ini, menetapkan bahwa:</p>

    <table class="meta-table">
        <tr>
            <td>Nama Lengkap</td>
            <td>: {{nama_lengkap}}</td>
        </tr>
        <tr>
            <td>Tempat, Tanggal Lahir</td>
            <td>: {{tempat_lahir}}, {{tgl_lahir}}</td>
        </tr>
        <tr>
            <td>NUPTK / NPK</td>
            <td>: {{nuptk}}</td>
        </tr>
        <tr>
            <td>Status Kepegawaian</td>
            <td>: {{nama_jurusan}}</td>
        </tr>
        <tr>
            <td>Ketugasan</td>
            <td>: {{ketugasan}}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>: {{p_studi}}</td>
        </tr>
        <tr>
            <td>Asal Madrasah / Sekolah</td>
            <td>: {{nama_kelas}}</td>
        </tr>
        <tr>
            <td>TMT</td>
            <td>: {{tmt}}</td>
        </tr>
    </table>

    <p>
        Dengan ini ditetapkan sebagai tenaga pada lembaga <strong>{{nama_kelas}}</strong>.
        Template ini dapat diubah bebas sesuai kebutuhan jenis SK yayasan.
    </p>

    <div class="signature-block">
        <p>Ditetapkan pada: {{tanggal_cetak}}</p>
        <p>Yayasan,</p>
        <br><br><br>
        <p><strong>(........................................)</strong></p>
    </div>
</div>
HTML;
    }

    protected function defaultCss(): string
    {
        return <<<'CSS'
body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
    line-height: 1.6;
    color: #111827;
}

.document {
    padding: 8px 12px;
}

.text-center {
    text-align: center;
}

.meta-table {
    width: 100%;
    border-collapse: collapse;
    margin: 18px 0;
}

.meta-table td {
    padding: 4px 0;
    vertical-align: top;
}

.meta-table td:first-child {
    width: 220px;
}

.signature-block {
    width: 280px;
    margin-left: auto;
    margin-top: 42px;
    text-align: left;
}
CSS;
    }

    protected function renderTemplate(SkTemplate $template, $user): string
    {
        return strtr((string) $template->content, $this->replacementMap($user));
    }

    protected function renderText(string $text, $user): string
    {
        return trim(strtr($text, $this->replacementMap($user, false)));
    }

    protected function replacementMap($user, bool $escapeHtml = true): array
    {
        Carbon::setLocale('id');

        $values = [
            'nama_lengkap' => $user->nama_lengkap ?? '',
            'email' => $user->email ?? '',
            'nis' => $user->nis ?? '',
            'nuptk' => $user->nuptk ?? '',
            'nip' => $user->nip ?? '',
            'tempat_lahir' => $user->tempat_lahir ?? '',
            'tgl_lahir' => $this->formatDate($user->tgl_lahir ?? null),
            'tmt' => $this->formatDate($user->tmt ?? null),
            'alamat' => $user->alamat ?? '',
            'alamat_html' => nl2br(e((string) ($user->alamat ?? ''))),
            'nama_kelas' => $user->nama_kelas ?? '',
            'nama_jurusan' => $user->nama_jurusan ?? '',
            'ketugasan' => $user->nama_ketugasan ?? '',
            'periode_sk' => $user->nama_periode ?? '',
            'ptt_lulus' => $user->ptt_lulus ?? '',
            'p_studi' => $user->p_studi ?? '',
            'tanggal_cetak' => now()->translatedFormat('d F Y'),
            'waktu_cetak' => now()->translatedFormat('d F Y H:i'),
            'tahun' => now()->format('Y'),
            'bulan' => now()->translatedFormat('F'),
        ];

        $map = [];
        foreach ($values as $key => $value) {
            $map['{{' . $key . '}}'] = $key === 'alamat_html'
                ? (string) $value
                : ($escapeHtml ? e((string) $value) : (string) $value);
        }

        return $map;
    }

    protected function formatDate($value): string
    {
        if (!$value) {
            return '';
        }

        try {
            return Carbon::parse($value)->translatedFormat('d F Y');
        } catch (\Throwable $exception) {
            return (string) $value;
        }
    }
}
