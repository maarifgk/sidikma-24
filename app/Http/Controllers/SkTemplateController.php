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
            'presetTitle' => 'SK Yayasan - {{nama_lengkap}}',
            'presetContent' => $this->defaultContent(),
            'presetCss' => $this->defaultCss(),
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
            'presetTitle' => 'SK Yayasan - {{nama_lengkap}}',
            'presetContent' => $this->defaultContent(),
            'presetCss' => $this->defaultCss(),
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
            ['key' => 'nomor_sk', 'label' => 'Nomor SK / nomor surat keputusan'],
            ['key' => 'tanggal_sk', 'label' => 'Tanggal penetapan SK'],
            ['key' => 'tanggal_mulai', 'label' => 'Tanggal mulai berlaku'],
            ['key' => 'tanggal_selesai', 'label' => 'Tanggal selesai berlaku'],
            ['key' => 'masa_sk', 'label' => 'Masa berlaku versi teks'],
            ['key' => 'gaji_pokok', 'label' => 'Nominal gaji pokok'],
            ['key' => 'tunjangan_lain', 'label' => 'Nominal tunjangan lain'],
            ['key' => 'tanggal_cetak', 'label' => 'Tanggal cetak dokumen'],
            ['key' => 'waktu_cetak', 'label' => 'Tanggal dan jam cetak'],
            ['key' => 'tahun', 'label' => 'Tahun sekarang'],
            ['key' => 'bulan', 'label' => 'Nama bulan sekarang'],
            ['key' => 'logo_url', 'label' => 'URL logo header'],
            ['key' => 'stempel_url', 'label' => 'URL gambar stempel'],
            ['key' => 'signature_url', 'label' => 'URL gambar tanda tangan'],
            ['key' => 'qr_url', 'label' => 'URL gambar QR / barcode'],
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
            'nomor_sk' => '0394/SK.01/LPM.GK/VI/2025',
            'tanggal_sk' => '30 Juni 2025',
            'tanggal_mulai' => '1 Juli 2025',
            'tanggal_selesai' => '30 Juni 2026',
            'masa_sk' => '1 Juli 2025 sampai dengan 30 Juni 2026',
            'gaji_pokok' => 'sesuai ketentuan yayasan',
            'tunjangan_lain' => 'sesuai ketentuan yang berlaku',
            'tanggal_cetak' => now()->translatedFormat('d F Y'),
            'waktu_cetak' => now()->translatedFormat('d F Y H:i'),
            'tahun' => now()->format('Y'),
            'bulan' => now()->translatedFormat('F'),
            'logo_url' => $this->placeholderSvgDataUri('LOGO NU', '#159947', '#ffffff', 'circle'),
            'stempel_url' => $this->placeholderSvgDataUri('STEMPEL', '#3cab62', '#ffffff', 'stamp'),
            'signature_url' => $this->placeholderSvgDataUri('TTD', '#3f51b5', '#ffffff', 'signature'),
            'qr_url' => $this->placeholderSvgDataUri('QR', '#111827', '#ffffff', 'qr'),
        ];
    }

    protected function defaultContent(): string
    {
        return <<<'HTML'
<div class="document">
    <table class="header-table">
        <tr>
            <td class="header-logo-cell">
                <img src="{{logo_url}}" alt="Logo" class="header-logo">
            </td>
            <td class="header-text-cell">
                <div class="header-topline">PENGURUS CABANG NAHDLATUL ULAMA GUNUNGKIDUL</div>
                <div class="header-title">LEMBAGA PENDIDIKAN MA'ARIF NU</div>
                <div class="header-address">Jln. Tentara Pelajar, Trimulyo I, Kepek, Wonosari, Gunungkidul-55813</div>
                <div class="header-contact">08522947609 &nbsp;&nbsp; maarifgunungkidul@gmail.com</div>
            </td>
        </tr>
    </table>

    <div class="header-divider"></div>

    <div class="text-center decision-heading">
        <div class="decision-title">SURAT KEPUTUSAN KETUA LP MA'ARIF NU GUNUNGKIDUL</div>
        <div class="decision-number">Nomor : {{nomor_sk}}</div>
    </div>

    <div class="content-block">
        <p class="opening-line">Ketua Lembaga Pendidikan Ma'arif NU Kabupaten Gunungkidul</p>

        <table class="consideration-table">
            <tr>
                <td class="label">Menimbang</td>
                <td class="colon">:</td>
                <td>
                    Bahwa demi meningkatkan kualitas pelayanan pendidikan di <strong>{{nama_kelas}}</strong>,
                    maka dipandang perlu mengangkat guru tetap yang memenuhi kualifikasi;<br>
                    Bahwa guru tersebut di bawah ini memenuhi syarat untuk diangkat sebagai guru tetap
                    di LP. Ma'arif NU PCNU Gunungkidul untuk <strong>{{nama_kelas}}</strong>, sesuai dengan kualifikasi tersebut;
                </td>
            </tr>
            <tr>
                <td class="label">Mengingat</td>
                <td class="colon">:</td>
                <td>
                    1. Undang-undang Nomor 20 Tahun 2003 tentang Sisdiknas;<br>
                    2. Pedoman Penyelenggaraan LP Ma'arif NU DIY No 01 Tahun 2023;<br>
                    3. Aturan Kepegawaian LP Ma'arif NU DIY No 04 Tahun 2023;
                </td>
            </tr>
            <tr>
                <td class="label">Memperhatikan</td>
                <td class="colon">:</td>
                <td>
                    Bahwa tenaga pendidik berikut berstatus aktif di <strong>{{nama_kelas}}</strong> sesuai
                    verifikasi data di Aplikasi SiDIKMa-GK pada tahun ditetapkannya keputusan ini;
                </td>
            </tr>
        </table>

        <div class="memutuskan-title">MEMUTUSKAN</div>

        <table class="decision-body-table">
            <tr>
                <td class="label">Menetapkan</td>
                <td class="colon">:</td>
                <td></td>
            </tr>
            <tr>
                <td class="label">Pertama</td>
                <td class="colon">:</td>
                <td>
                    Guru tersebut di bawah ini :
                    <table class="identity-table">
                        <tr><td class="num">1.</td><td class="field">Nama</td><td class="colon">:</td><td>{{nama_lengkap}}</td></tr>
                        <tr><td class="num">2.</td><td class="field">Tempat, tanggal lahir</td><td class="colon">:</td><td>{{tempat_lahir}}, {{tgl_lahir}}</td></tr>
                        <tr><td class="num">3.</td><td class="field">NUPTK/NPK</td><td class="colon">:</td><td>{{nuptk}}</td></tr>
                        <tr><td class="num">4.</td><td class="field">EWANUGK/KARTANU</td><td class="colon">:</td><td>{{nis}}</td></tr>
                        <tr><td class="num">5.</td><td class="field">TMT Pertama</td><td class="colon">:</td><td>{{tmt}}</td></tr>
                        <tr><td class="num">6.</td><td class="field">Pendidikan, tahun lulus</td><td class="colon">:</td><td>{{ptt_lulus}}</td></tr>
                        <tr><td class="num">7.</td><td class="field">Program Studi</td><td class="colon">:</td><td>{{p_studi}}</td></tr>
                        <tr><td class="num">8.</td><td class="field">Status Kepegawaian</td><td class="colon">:</td><td>{{nama_jurusan}}</td></tr>
                    </table>
                    Diangkat kembali sebagai tenaga pendidik LP. Ma'arif NU PCNU Gunungkidul untuk
                    <strong>{{nama_kelas}}</strong> tahun pelajaran 2025/2026 dengan ketugasan
                    <strong>{{ketugasan}}</strong>, dan kepadanya diberikan gaji pokok sebesar
                    <strong>{{gaji_pokok}}</strong> serta tunjangan lain <strong>{{tunjangan_lain}}</strong>.
                </td>
            </tr>
            <tr>
                <td class="label">Kedua</td>
                <td class="colon">:</td>
                <td>
                    Keputusan ini berlaku terhitung mulai tanggal <strong>{{tanggal_mulai}}</strong> sampai dengan
                    <strong>{{tanggal_selesai}}</strong> yang apabila di kemudian hari terdapat kekeliruan di dalamnya,
                    akan diadakan perbaikan dan perhitungan kembali sebagaimana mestinya.
                </td>
            </tr>
            <tr>
                <td class="label">Ketiga</td>
                <td class="colon">:</td>
                <td>Asli surat keputusan ini diberikan kepada yang bersangkutan.</td>
            </tr>
        </table>

        <div class="signature-section">
            <table class="signature-table">
                <tr><td>Ditetapkan di</td><td class="colon">:</td><td>Gunungkidul</td></tr>
                <tr><td>Pada Tanggal</td><td class="colon">:</td><td>{{tanggal_sk}}</td></tr>
                <tr><td colspan="3">Pengurus LP Ma'arif NU Kab. Gunungkidul</td></tr>
                <tr><td colspan="3">Ketua,</td></tr>
            </table>

            <div class="signature-visuals">
                <img src="{{stempel_url}}" alt="Stempel" class="stamp-image">
                <img src="{{signature_url}}" alt="Tanda Tangan" class="signature-image">
            </div>

            <div class="signature-name">Drs. H. SANGKIN, M.Pd.</div>
        </div>

        <div class="footer-section">
            <div class="tembusan-block">
                <div>Tembusan Yth;</div>
                <ol>
                    <li>Kepala Kemenag Kab. Gunungkidul</li>
                    <li>Kepala {{nama_kelas}}</li>
                    <li>Arsip</li>
                </ol>
            </div>
            <div class="qr-block">
                <img src="{{qr_url}}" alt="QR" class="qr-image">
            </div>
        </div>
    </div>
</div>
HTML;
    }

    protected function defaultCss(): string
    {
        return <<<'CSS'
body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 11.5px;
    line-height: 1.33;
    color: #000000;
}

.document {
    padding: 4px 10px 0;
}

.text-center {
    text-align: center;
}

.header-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 4px;
}

.header-logo-cell {
    width: 160px;
    text-align: center;
    vertical-align: middle;
}

.header-text-cell {
    text-align: center;
    vertical-align: middle;
}

.header-logo {
    width: 145px;
    height: 145px;
    object-fit: contain;
}

.header-topline {
    font-size: 13px;
    font-weight: 700;
    color: #159947;
    text-transform: uppercase;
}

.header-title {
    font-size: 26px;
    font-weight: 800;
    color: #0b8f2f;
    text-transform: uppercase;
    margin: 6px 0 4px;
}

.header-address,
.header-contact {
    color: #159947;
    font-size: 11px;
    font-weight: 700;
}

.header-divider {
    border-top: 4px solid #0b8f2f;
    margin: 8px 0 4px;
}

.decision-heading {
    margin-bottom: 20px;
}

.decision-title {
    font-weight: 800;
    text-decoration: underline;
    font-size: 13px;
}

.decision-number {
    margin-top: 3px;
    font-size: 12px;
}

.opening-line {
    margin: 0 0 10px;
}

.content-block {
    margin-top: 10px;
}

.consideration-table,
.decision-body-table,
.signature-table {
    width: 100%;
    border-collapse: collapse;
}

.consideration-table td,
.decision-body-table td,
.signature-table td {
    vertical-align: top;
    padding: 1px 0;
}

.label {
    width: 118px;
    font-weight: 700;
}

.colon {
    width: 14px;
    text-align: center;
}

.memutuskan-title {
    text-align: center;
    font-weight: 800;
    font-size: 15px;
    margin: 10px 0 6px;
}

.identity-table {
    width: 100%;
    border-collapse: collapse;
    margin: 4px 0 6px;
}

.identity-table td {
    padding: 0;
    vertical-align: top;
}

.identity-table .num {
    width: 18px;
    text-align: right;
    padding-right: 8px;
}

.identity-table .field {
    width: 170px;
}

.signature-section {
    width: 320px;
    margin-left: auto;
    margin-top: 12px;
}

.signature-visuals {
    position: relative;
    height: 132px;
    margin-top: 6px;
}

.stamp-image {
    position: absolute;
    left: 0;
    bottom: 0;
    width: 132px;
    height: 132px;
    object-fit: contain;
}

.signature-image {
    position: absolute;
    left: 86px;
    top: 6px;
    width: 118px;
    height: 76px;
    object-fit: contain;
}

.signature-name {
    font-size: 12px;
    font-weight: 800;
    margin-top: 2px;
}

.footer-section {
    margin-top: 18px;
    width: 100%;
    overflow: hidden;
}

.tembusan-block {
    float: left;
    width: 72%;
}

.tembusan-block ol {
    margin: 4px 0 0 22px;
    padding: 0;
}

.tembusan-block li {
    margin-bottom: 2px;
}

.qr-block {
    float: right;
    width: 90px;
    text-align: right;
}

.qr-image {
    width: 84px;
    height: 84px;
    object-fit: contain;
}

.document::after {
    content: '';
    display: block;
    clear: both;
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
            'nomor_sk' => '0394/SK.01/LPM.GK/VI/' . now()->format('Y'),
            'tanggal_sk' => now()->translatedFormat('d F Y'),
            'tanggal_mulai' => '1 Juli ' . now()->format('Y'),
            'tanggal_selesai' => '30 Juni ' . now()->addYear()->format('Y'),
            'masa_sk' => '1 Juli ' . now()->format('Y') . ' sampai dengan 30 Juni ' . now()->addYear()->format('Y'),
            'gaji_pokok' => 'sesuai ketentuan yayasan',
            'tunjangan_lain' => 'sesuai ketentuan yang berlaku',
            'tanggal_cetak' => now()->translatedFormat('d F Y'),
            'waktu_cetak' => now()->translatedFormat('d F Y H:i'),
            'tahun' => now()->format('Y'),
            'bulan' => now()->translatedFormat('F'),
            'logo_url' => $this->placeholderSvgDataUri('LOGO NU', '#159947', '#ffffff', 'circle'),
            'stempel_url' => $this->placeholderSvgDataUri('STEMPEL', '#3cab62', '#ffffff', 'stamp'),
            'signature_url' => $this->placeholderSvgDataUri('TTD', '#3f51b5', '#ffffff', 'signature'),
            'qr_url' => $this->placeholderSvgDataUri('QR', '#111827', '#ffffff', 'qr'),
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

    protected function placeholderSvgDataUri(string $text, string $primaryColor, string $secondaryColor, string $type): string
    {
        $svg = match ($type) {
            'stamp' => '
                <svg xmlns="http://www.w3.org/2000/svg" width="240" height="240" viewBox="0 0 240 240">
                    <circle cx="120" cy="120" r="102" fill="none" stroke="' . $primaryColor . '" stroke-width="8"/>
                    <circle cx="120" cy="120" r="78" fill="none" stroke="' . $primaryColor . '" stroke-width="4"/>
                    <text x="120" y="108" text-anchor="middle" font-size="16" font-family="Arial" fill="' . $primaryColor . '" font-weight="700">LEMBAGA PENDIDIKAN</text>
                    <text x="120" y="132" text-anchor="middle" font-size="24" font-family="Arial" fill="' . $primaryColor . '" font-weight="800">' . $text . '</text>
                    <text x="120" y="156" text-anchor="middle" font-size="16" font-family="Arial" fill="' . $primaryColor . '" font-weight="700">MA\'ARIF NU</text>
                </svg>',
            'signature' => '
                <svg xmlns="http://www.w3.org/2000/svg" width="360" height="180" viewBox="0 0 360 180">
                    <path d="M20 135 C60 20, 120 20, 135 100 S190 170, 220 65 S290 10, 335 115" fill="none" stroke="' . $primaryColor . '" stroke-width="8" stroke-linecap="round"/>
                    <path d="M100 145 C145 90, 180 120, 215 45" fill="none" stroke="' . $primaryColor . '" stroke-width="6" stroke-linecap="round"/>
                </svg>',
            'qr' => '
                <svg xmlns="http://www.w3.org/2000/svg" width="180" height="180" viewBox="0 0 180 180">
                    <rect width="180" height="180" fill="' . $secondaryColor . '"/>
                    <rect x="10" y="10" width="50" height="50" fill="' . $primaryColor . '"/>
                    <rect x="20" y="20" width="30" height="30" fill="' . $secondaryColor . '"/>
                    <rect x="120" y="10" width="50" height="50" fill="' . $primaryColor . '"/>
                    <rect x="130" y="20" width="30" height="30" fill="' . $secondaryColor . '"/>
                    <rect x="10" y="120" width="50" height="50" fill="' . $primaryColor . '"/>
                    <rect x="20" y="130" width="30" height="30" fill="' . $secondaryColor . '"/>
                    <rect x="80" y="78" width="12" height="12" fill="' . $primaryColor . '"/>
                    <rect x="96" y="78" width="12" height="12" fill="' . $primaryColor . '"/>
                    <rect x="112" y="78" width="12" height="12" fill="' . $primaryColor . '"/>
                    <rect x="80" y="94" width="12" height="12" fill="' . $primaryColor . '"/>
                    <rect x="112" y="94" width="12" height="12" fill="' . $primaryColor . '"/>
                    <rect x="80" y="110" width="12" height="12" fill="' . $primaryColor . '"/>
                    <rect x="96" y="110" width="12" height="12" fill="' . $primaryColor . '"/>
                    <rect x="112" y="110" width="12" height="12" fill="' . $primaryColor . '"/>
                    <text x="90" y="164" text-anchor="middle" font-size="18" font-family="Arial" fill="' . $primaryColor . '" font-weight="700">' . $text . '</text>
                </svg>',
            default => '
                <svg xmlns="http://www.w3.org/2000/svg" width="260" height="260" viewBox="0 0 260 260">
                    <circle cx="130" cy="130" r="108" fill="' . $secondaryColor . '" stroke="' . $primaryColor . '" stroke-width="10"/>
                    <circle cx="130" cy="130" r="58" fill="none" stroke="' . $primaryColor . '" stroke-width="6"/>
                    <text x="130" y="96" text-anchor="middle" font-size="22" font-family="Arial" fill="' . $primaryColor . '" font-weight="700">LP MA\'ARIF</text>
                    <text x="130" y="132" text-anchor="middle" font-size="36" font-family="Arial" fill="' . $primaryColor . '" font-weight="800">NU</text>
                    <text x="130" y="168" text-anchor="middle" font-size="20" font-family="Arial" fill="' . $primaryColor . '" font-weight="700">' . $text . '</text>
                </svg>',
        };

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
