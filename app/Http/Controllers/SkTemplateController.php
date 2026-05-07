<?php

namespace App\Http\Controllers;

use App\Models\SkTemplate;
use App\Models\SkYayasanSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
            'settingsCount' => SkYayasanSetting::query()->count(),
        ]);
    }

    public function create()
    {
        $this->ensureRoleOne();

        $defaultBuilderData = $this->defaultBuilderData();

        return view('backend.sk_templates.form', [
            'title' => 'Buat Template SK',
            'template' => new SkTemplate([
                'paper_size' => 'A4',
                'orientation' => 'portrait',
                'builder_data' => $defaultBuilderData,
                'document_title' => 'SK Yayasan - {{nama_lengkap}}',
                'content' => $this->defaultContent(),
                'custom_css' => $this->defaultCss(),
                'is_active' => true,
            ]),
            'builderData' => $defaultBuilderData,
            'placeholders' => $this->placeholderDefinitions(),
            'previewSamples' => $this->previewSamples(),
            'whatsappIconUrl' => $this->placeholderSvgDataUri('', '#16a34a', '#ffffff', 'whatsapp'),
            'emailIconUrl' => $this->placeholderSvgDataUri('', '#0f766e', '#ffffff', 'email'),
            'presetTitle' => 'SK Yayasan - {{nama_lengkap}}',
            'presetContent' => $this->buildContentFromBuilderData($defaultBuilderData),
            'presetCss' => $this->defaultCss(),
            'presetBuilderData' => $defaultBuilderData,
            'formAction' => route('sk-templates.store'),
            'submitLabel' => 'Simpan Template',
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureRoleOne();

        [$payload, $builderData] = $this->validatedPayload($request);
        $payload['slug'] = $this->makeUniqueSlug($payload['name']);
        $payload['is_active'] = $request->boolean('is_active');
        $payload['builder_data'] = $builderData;
        $payload['custom_css'] = $this->defaultCss();
        $payload['content'] = $this->buildContentFromBuilderData($builderData);

        SkTemplate::create($payload);

        Alert::success('Template SK berhasil ditambahkan');

        return redirect()->route('sk-templates.index');
    }

    public function show(SkTemplate $skTemplate, Request $request)
    {
        $this->ensureRoleOne();

        $selectedUserId = $request->integer('user_id');
        $selectedUser = $selectedUserId ? $this->findUserOrFail($selectedUserId) : null;
        $previewSkNumber = $selectedUser ? $this->previewSkNumberForUser($selectedUser) : null;
        $renderedHtml = $selectedUser ? $this->renderTemplate($skTemplate, $selectedUser, [
            'nomor_sk' => $previewSkNumber,
        ]) : null;

        return view('backend.sk_templates.show', [
            'title' => 'Generate PDF SK',
            'template' => $skTemplate,
            'users' => $this->templateUsers(),
            'selectedUser' => $selectedUser,
            'selectedUserIds' => array_map('intval', (array) $request->input('user_ids', [])),
            'previewSkNumber' => $previewSkNumber,
            'renderedHtml' => $renderedHtml,
            'placeholders' => $this->placeholderDefinitions(),
        ]);
    }

    public function settings()
    {
        $this->ensureRoleOne();

        $periods = $this->periods();
        $settings = SkYayasanSetting::query()->get()->keyBy('periode_id');

        return view('backend.sk_templates.settings', [
            'title' => 'Pengaturan SK Yayasan',
            'periods' => $periods,
            'settingsData' => $this->settingsFormData($periods, $settings),
            'patternHelp' => [
                '{{nomor_urut}}' => 'Nomor urut dengan nol di depan sesuai digit',
                '{{nomor_urut_raw}}' => 'Nomor urut tanpa nol di depan',
                '{{periode}}' => 'Nama periode',
                '{{periode_upper}}' => 'Nama periode huruf besar',
                '{{tahun}}' => 'Tahun sekarang',
                '{{bulan_romawi}}' => 'Bulan sekarang dalam angka Romawi',
            ],
        ]);
    }

    public function updateSettings(Request $request)
    {
        $this->ensureRoleOne();

        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.nomor_pattern' => 'required|string|max:255',
            'settings.*.nomor_awal' => 'required|integer|min:1|max:999999',
            'settings.*.nomor_berikutnya' => 'required|integer|min:1|max:999999',
            'settings.*.digit_nomor' => 'required|integer|min:1|max:10',
            'settings.*.is_active' => 'nullable|boolean',
        ]);

        $periodIds = $this->periods()->pluck('id')->map(fn ($id) => (int) $id)->all();

        foreach ($periodIds as $periodId) {
            $input = $validated['settings'][$periodId] ?? null;
            if (!$input) {
                continue;
            }

            SkYayasanSetting::query()->updateOrCreate(
                ['periode_id' => $periodId],
                [
                    'nomor_pattern' => $input['nomor_pattern'],
                    'nomor_awal' => (int) $input['nomor_awal'],
                    'nomor_berikutnya' => max((int) $input['nomor_awal'], (int) $input['nomor_berikutnya']),
                    'digit_nomor' => (int) $input['digit_nomor'],
                    'is_active' => (bool) ($input['is_active'] ?? false),
                ]
            );
        }

        Alert::success('Pengaturan SK Yayasan berhasil diperbarui');

        return redirect()->route('sk-templates.settings');
    }

    public function edit(SkTemplate $skTemplate)
    {
        $this->ensureRoleOne();

        $builderData = $this->normalizeBuilderData($skTemplate->builder_data ?? []);

        return view('backend.sk_templates.form', [
            'title' => 'Edit Template SK',
            'template' => $skTemplate,
            'builderData' => $builderData,
            'placeholders' => $this->placeholderDefinitions(),
            'previewSamples' => $this->previewSamples(),
            'whatsappIconUrl' => $this->placeholderSvgDataUri('', '#16a34a', '#ffffff', 'whatsapp'),
            'emailIconUrl' => $this->placeholderSvgDataUri('', '#0f766e', '#ffffff', 'email'),
            'presetTitle' => 'SK Yayasan - {{nama_lengkap}}',
            'presetContent' => $this->buildContentFromBuilderData($this->defaultBuilderData()),
            'presetCss' => $this->defaultCss(),
            'presetBuilderData' => $this->defaultBuilderData(),
            'formAction' => route('sk-templates.update', $skTemplate),
            'submitLabel' => 'Update Template',
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, SkTemplate $skTemplate)
    {
        $this->ensureRoleOne();

        [$payload, $builderData] = $this->validatedPayload($request);
        $payload['slug'] = $this->makeUniqueSlug($payload['name'], $skTemplate->id);
        $payload['is_active'] = $request->boolean('is_active');
        $payload['builder_data'] = $builderData;
        $payload['custom_css'] = $this->defaultCss();
        $payload['content'] = $this->buildContentFromBuilderData($builderData);

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
        $skNumber = $this->previewSkNumberForUser($user);

        return view('backend.sk_templates.preview', [
            'title' => $this->renderText($skTemplate->document_title, $user, ['nomor_sk' => $skNumber]),
            'html' => $this->renderTemplate($skTemplate, $user, ['nomor_sk' => $skNumber]),
            'customCss' => $skTemplate->custom_css,
        ]);
    }

    public function pdf(SkTemplate $skTemplate, $userId)
    {
        $this->ensureRoleOne();

        $user = $this->findUserOrFail($userId);
        $skNumber = $this->previewSkNumberForUser($user);
        $documentTitle = $this->renderText($skTemplate->document_title, $user, ['nomor_sk' => $skNumber]);

        $pdf = Pdf::loadView('backend.sk_templates.pdf', [
            'title' => $documentTitle,
            'html' => $this->renderTemplate($skTemplate, $user, ['nomor_sk' => $skNumber]),
            'customCss' => $skTemplate->custom_css,
        ])->setPaper($skTemplate->paper_size ?: 'A4', $skTemplate->orientation === 'landscape' ? 'landscape' : 'portrait');

        return $pdf->stream(Str::slug($documentTitle ?: $skTemplate->name ?: 'template-sk') . '.pdf');
    }

    public function batchPdf(SkTemplate $skTemplate, Request $request)
    {
        $this->ensureRoleOne();

        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'required|integer',
        ]);

        $orderedIds = array_values(array_unique(array_map('intval', $validated['user_ids'])));
        $users = $this->orderedTemplateUsers($orderedIds);
        abort_if($users->isEmpty(), 404);

        [$combinedHtml, $documentTitle] = DB::transaction(function () use ($orderedIds, $users, $skTemplate) {
            $periodIds = $users->pluck('periode')->filter()->map(fn ($value) => (int) $value)->unique()->values()->all();
            $settings = SkYayasanSetting::query()
                ->whereIn('periode_id', $periodIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('periode_id');

            $counters = [];
            $htmlParts = [];

            foreach ($users as $user) {
                $periodId = (int) ($user->periode ?? 0);
                $setting = $this->resolveNumberSettingForPeriod($periodId, $settings, true);
                $currentNumber = $counters[$periodId] ?? max((int) $setting->nomor_awal, (int) $setting->nomor_berikutnya);
                $generatedSkNumber = $this->formatSkNumber($setting, $currentNumber, $user);

                $htmlParts[] = $this->renderTemplate($skTemplate, $user, [
                    'nomor_sk' => $generatedSkNumber,
                ]);

                $counters[$periodId] = $currentNumber + 1;
            }

            foreach ($counters as $periodId => $nextNumber) {
                $setting = $this->resolveNumberSettingForPeriod($periodId, $settings, true);
                $setting->nomor_berikutnya = $nextNumber;
                $setting->save();
            }

            return [
                implode('<div style="page-break-after: always;"></div>', $htmlParts),
                ($skTemplate->name ?: 'SK Yayasan') . ' Batch ' . now()->format('Ymd-His'),
            ];
        });

        $pdf = Pdf::loadView('backend.sk_templates.pdf', [
            'title' => $documentTitle,
            'html' => $combinedHtml,
            'customCss' => $skTemplate->custom_css,
        ])->setPaper($skTemplate->paper_size ?: 'A4', $skTemplate->orientation === 'landscape' ? 'landscape' : 'portrait');

        return $pdf->stream(Str::slug($documentTitle ?: $skTemplate->name ?: 'template-sk-batch') . '.pdf');
    }

    protected function ensureRoleOne(): void
    {
        abort_unless(request()->user() && (int) request()->user()->role === 1, 403);
    }

    protected function validatedPayload(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document_title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'paper_size' => 'required|in:A4,legal,letter',
            'orientation' => 'required|in:portrait,landscape',
            'header_topline' => 'required|string|max:255',
            'header_title' => 'required|string|max:255',
            'header_address' => 'required|string|max:500',
            'header_phone' => 'required|string|max:255',
            'header_email' => 'required|string|max:255',
            'header_topline_font_size' => 'required|numeric|between:8,40',
            'header_title_font_size' => 'required|numeric|between:8,40',
            'header_address_font_size' => 'required|numeric|between:8,40',
            'header_contact_font_size' => 'required|numeric|between:8,40',
            'decision_title' => 'required|string|max:255',
            'nomor_sk' => 'required|string|max:255',
            'opening_line' => 'required|string|max:255',
            'decision_title_font_size' => 'required|numeric|between:8,40',
            'decision_number_font_size' => 'required|numeric|between:8,40',
            'opening_line_font_size' => 'required|numeric|between:8,40',
            'menimbang_text' => 'required|string',
            'mengingat_text' => 'required|string',
            'memperhatikan_text' => 'required|string',
            'consideration_font_size' => 'required|numeric|between:8,40',
            'memutuskan_title' => 'required|string|max:255',
            'pertama_intro' => 'required|string|max:255',
            'pertama_penutup' => 'required|string',
            'kedua_text' => 'required|string',
            'ketiga_text' => 'required|string',
            'memutuskan_title_font_size' => 'required|numeric|between:8,40',
            'decision_body_font_size' => 'required|numeric|between:8,40',
            'identity_table_font_size' => 'required|numeric|between:8,40',
            'signature_city' => 'required|string|max:255',
            'signature_date_label' => 'required|string|max:255',
            'signature_date' => 'required|string|max:255',
            'signature_body_top' => 'required|string|max:255',
            'signature_role' => 'required|string|max:255',
            'signature_name' => 'required|string|max:255',
            'signature_font_size' => 'required|numeric|between:8,40',
            'tembusan_title' => 'required|string|max:255',
            'tembusan_items' => 'required|string',
            'tembusan_title_font_size' => 'required|numeric|between:8,40',
            'tembusan_items_font_size' => 'required|numeric|between:8,40',
            'logo_url' => 'nullable|string',
            'logo_file' => 'nullable|file|mimes:jpg,jpeg,png,webp,svg|max:4096',
        ]);

        if ($request->hasFile('logo_file')) {
            $logoFile = $request->file('logo_file');
            $validated['logo_url'] = 'data:' . $logoFile->getMimeType() . ';base64,' . base64_encode(file_get_contents($logoFile->getRealPath()));
        }

        $builderData = $this->normalizeBuilderData($validated);

        return [[
            'name' => $validated['name'],
            'document_title' => $validated['document_title'],
            'description' => $validated['description'] ?? null,
            'paper_size' => $validated['paper_size'],
            'orientation' => $validated['orientation'],
        ], $builderData];
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

    protected function orderedTemplateUsers(array $orderedIds): Collection
    {
        $usersById = $this->usersQuery()
            ->whereIn('users.id', $orderedIds)
            ->get()
            ->keyBy('id');

        return collect($orderedIds)
            ->map(fn ($id) => $usersById->get($id))
            ->filter()
            ->values();
    }

    protected function findUserOrFail(int $userId)
    {
        return $this->usersQuery()->where('users.id', $userId)->firstOrFail();
    }

    protected function periods(): Collection
    {
        return DB::table('periode')
            ->select('id', 'nama_periode')
            ->orderBy('id')
            ->get();
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
            ['key' => 'logo_url', 'label' => 'Logo kop surat'],
        ];
    }

    protected function settingsFormData(Collection $periods, Collection $settings): array
    {
        $rows = [];

        foreach ($periods as $period) {
            $setting = $settings->get($period->id);
            $rows[(int) $period->id] = [
                'nomor_pattern' => old("settings.{$period->id}.nomor_pattern", $setting->nomor_pattern ?? $this->defaultSkNumberPattern($period->nama_periode)),
                'nomor_awal' => old("settings.{$period->id}.nomor_awal", $setting->nomor_awal ?? 1),
                'nomor_berikutnya' => old("settings.{$period->id}.nomor_berikutnya", $setting->nomor_berikutnya ?? 1),
                'digit_nomor' => old("settings.{$period->id}.digit_nomor", $setting->digit_nomor ?? 4),
                'is_active' => old("settings.{$period->id}.is_active", $setting->is_active ?? true),
            ];
        }

        return $rows;
    }

    protected function defaultSkNumberPattern(?string $periodName = null): string
    {
        return '{{nomor_urut}}/SK.01/LPM.GK/' . strtoupper((string) ($periodName ?: '{{periode}}')) . '/{{tahun}}';
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
        ];
    }

    protected function defaultBuilderData(): array
    {
        return [
            'header_topline' => 'PENGURUS CABANG NAHDLATUL ULAMA GUNUNGKIDUL',
            'header_title' => "LEMBAGA PENDIDIKAN MA'ARIF NU",
            'header_address' => 'Jln. Tentara Pelajar, Trimulyo I, Kepek, Wonosari, Gunungkidul-55813',
            'header_phone' => '08522947609',
            'header_email' => 'maarifgunungkidul@gmail.com',
            'header_topline_font_size' => '13',
            'header_title_font_size' => '26',
            'header_address_font_size' => '11',
            'header_contact_font_size' => '11',
            'decision_title' => "SURAT KEPUTUSAN KETUA LP MA'ARIF NU GUNUNGKIDUL",
            'nomor_sk' => '{{nomor_sk}}',
            'opening_line' => "Ketua Lembaga Pendidikan Ma'arif NU Kabupaten Gunungkidul",
            'decision_title_font_size' => '13',
            'decision_number_font_size' => '12',
            'opening_line_font_size' => '11.5',
            'menimbang_text' => "Bahwa demi meningkatkan kualitas pelayanan pendidikan di {{nama_kelas}}, maka dipandang perlu mengangkat guru tetap yang memenuhi kualifikasi;\nBahwa guru tersebut di bawah ini memenuhi syarat untuk diangkat sebagai Guru Tetap di LP. Ma'arif NU PCNU Gunungkidul untuk {{nama_kelas}}, sesuai dengan kualifikasi tersebut;",
            'mengingat_text' => "1. Undang-undang Nomor 20 Tahun 2003 tentang Sisdiknas;\n2. Pedoman Penyelenggaraan LP Ma'arif NU DIY No 01 Tahun 2023;\n3. Aturan Kepegawaian LP Ma'arif NU DIY No 04 Tahun 2023;",
            'memperhatikan_text' => "Bahwa tenaga pendidik berikut, berstatus aktif di {{nama_kelas}} sesuai dengan verifikasi data di Aplikasi SiDIKMa-GK pada tahun ditetapkannya keputusan ini;",
            'consideration_font_size' => '11.5',
            'memutuskan_title' => 'MEMUTUSKAN',
            'pertama_intro' => 'Guru tersebut di bawah ini :',
            'pertama_penutup' => "Diangkat kembali sebagai tenaga pendidik LP. Ma'arif NU PCNU Gunungkidul untuk {{nama_kelas}} tahun pelajaran 2025/2026 dengan ketugasan {{ketugasan}}, dan kepadanya diberikan gaji pokok serta tunjangan lain yang berlaku di {{nama_kelas}}.",
            'kedua_text' => 'Keputusan ini berlaku terhitung mulai tanggal {{tanggal_mulai}} sampai dengan {{tanggal_selesai}} yang apabila di kemudian hari terdapat kekeliruan di dalamnya, akan diadakan perbaikan dan perhitungan kembali sebagaimana mestinya.',
            'ketiga_text' => 'Asli surat keputusan ini diberikan kepada yang bersangkutan.',
            'memutuskan_title_font_size' => '15',
            'decision_body_font_size' => '11.5',
            'identity_table_font_size' => '11.5',
            'signature_city' => 'Gunungkidul',
            'signature_date_label' => 'Pada Tanggal',
            'signature_date' => '{{tanggal_sk}}',
            'signature_body_top' => "Pengurus LP Ma'arif NU Kab. Gunungkidul",
            'signature_role' => 'Ketua,',
            'signature_name' => 'Drs. H. SANGKIN, M.Pd.',
            'signature_font_size' => '11.5',
            'tembusan_title' => 'Tembusan Yth;',
            'tembusan_items' => "Kepala Kemenag Kab. Gunungkidul\nKepala {{nama_kelas}}\nArsip",
            'tembusan_title_font_size' => '11.5',
            'tembusan_items_font_size' => '11.5',
            'logo_url' => '{{logo_url}}',
        ];
    }

    protected function normalizeBuilderData(array $data): array
    {
        $defaults = $this->defaultBuilderData();
        $normalized = [];
        $legacyHeaderContact = isset($data['header_contact']) ? (string) $data['header_contact'] : '';
        [$legacyPhone, $legacyEmail] = $this->splitLegacyHeaderContact($legacyHeaderContact);

        foreach ($defaults as $key => $defaultValue) {
            if (array_key_exists($key, $data) && $data[$key] !== null) {
                $normalized[$key] = (string) $data[$key];
                continue;
            }

            if ($key === 'header_phone' && $legacyPhone !== '') {
                $normalized[$key] = $legacyPhone;
                continue;
            }

            if ($key === 'header_email' && $legacyEmail !== '') {
                $normalized[$key] = $legacyEmail;
                continue;
            }

            $normalized[$key] = $defaultValue;
        }

        return $normalized;
    }

    protected function defaultContent(): string
    {
        return $this->buildContentFromBuilderData($this->defaultBuilderData());
    }

    protected function buildContentFromBuilderData(array $builderData): string
    {
        $builderData = $this->normalizeBuilderData($builderData);
        $tembusanItems = array_values(array_filter(array_map('trim', preg_split("/\r\n|\n|\r/", $builderData['tembusan_items'])), fn ($item) => $item !== ''));
        $tembusanHtml = collect($tembusanItems)->map(function ($item) {
            return '<li>' . $this->formatBuilderText($item) . '</li>';
        })->implode("\n");

        return strtr(<<<'HTML'
<div class="document">
    <table class="header-table">
        <tr>
            <td class="header-logo-cell">
                <img src="__LOGO_URL__" alt="Logo" class="header-logo">
            </td>
            <td class="header-text-cell">
                <div class="header-topline" style="font-size: __HEADER_TOPLINE_FONT_SIZE__px;">__HEADER_TOPLINE__</div>
                <div class="header-title" style="font-size: __HEADER_TITLE_FONT_SIZE__px;">__HEADER_TITLE__</div>
                <div class="header-address" style="font-size: __HEADER_ADDRESS_FONT_SIZE__px;">__HEADER_ADDRESS__</div>
                <table class="header-contact-table">
                    <tr>
                        <td class="header-contact-text" style="font-size: __HEADER_CONTACT_FONT_SIZE__px;">__HEADER_PHONE__</td>
                        <td class="header-contact-icon-cell"><img src="__WHATSAPP_ICON_URL__" alt="WhatsApp" class="header-contact-icon"></td>
                    </tr>
                    <tr>
                        <td class="header-contact-text" style="font-size: __HEADER_CONTACT_FONT_SIZE__px;">__HEADER_EMAIL__</td>
                        <td class="header-contact-icon-cell"><img src="__EMAIL_ICON_URL__" alt="Email" class="header-contact-icon"></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="header-divider"></div>

    <div class="text-center decision-heading">
        <div class="decision-title" style="font-size: __DECISION_TITLE_FONT_SIZE__px;">__DECISION_TITLE__</div>
        <div class="decision-number" style="font-size: __DECISION_NUMBER_FONT_SIZE__px;">Nomor : __NOMOR_SK__</div>
    </div>

    <div class="content-block">
        <p class="opening-line" style="font-size: __OPENING_LINE_FONT_SIZE__px;">__OPENING_LINE__</p>

        <table class="consideration-table" style="font-size: __CONSIDERATION_FONT_SIZE__px;">
            <tr>
                <td class="label">Menimbang</td>
                <td class="colon">:</td>
                <td><div class="justified-text">__MENIMBANG_TEXT__</div></td>
            </tr>
            <tr>
                <td class="label">Mengingat</td>
                <td class="colon">:</td>
                <td><div class="justified-text">__MENGINGAT_TEXT__</div></td>
            </tr>
            <tr>
                <td class="label">Memperhatikan</td>
                <td class="colon">:</td>
                <td><div class="justified-text">__MEMPERHATIKAN_TEXT__</div></td>
            </tr>
        </table>

        <div class="memutuskan-title" style="font-size: __MEMUTUSKAN_TITLE_FONT_SIZE__px;">__MEMUTUSKAN_TITLE__</div>

        <table class="decision-body-table" style="font-size: __DECISION_BODY_FONT_SIZE__px;">
            <tr>
                <td class="label">Menetapkan</td>
                <td class="colon">:</td>
                <td></td>
            </tr>
            <tr>
                <td class="label">Pertama</td>
                <td class="colon">:</td>
                <td>
                    __PERTAMA_INTRO__
                    <table class="identity-table" style="font-size: __IDENTITY_TABLE_FONT_SIZE__px;">
                        <tr><td class="num">1.</td><td class="field">Nama</td><td class="colon">:</td><td>{{nama_lengkap}}</td></tr>
                        <tr><td class="num">2.</td><td class="field">Tempat, tanggal lahir</td><td class="colon">:</td><td>{{tempat_lahir}}, {{tgl_lahir}}</td></tr>
                        <tr><td class="num">3.</td><td class="field">NUPTK/NPK</td><td class="colon">:</td><td>{{nuptk}}</td></tr>
                        <tr><td class="num">4.</td><td class="field">EWANUGK/KARTANU</td><td class="colon">:</td><td>{{nis}}</td></tr>
                        <tr><td class="num">5.</td><td class="field">TMT Pertama</td><td class="colon">:</td><td>{{tmt}}</td></tr>
                        <tr><td class="num">6.</td><td class="field">Pendidikan, tahun lulus</td><td class="colon">:</td><td>{{ptt_lulus}}</td></tr>
                        <tr><td class="num">7.</td><td class="field">Program Studi</td><td class="colon">:</td><td>{{p_studi}}</td></tr>
                        <tr><td class="num">8.</td><td class="field">Status Kepegawaian</td><td class="colon">:</td><td>{{nama_jurusan}}</td></tr>
                    </table>
                    <div class="justified-text">__PERTAMA_PENUTUP__</div>
                </td>
            </tr>
            <tr>
                <td class="label">Kedua</td>
                <td class="colon">:</td>
                <td><div class="justified-text">__KEDUA_TEXT__</div></td>
            </tr>
            <tr>
                <td class="label">Ketiga</td>
                <td class="colon">:</td>
                <td><div class="justified-text">__KETIGA_TEXT__</div></td>
            </tr>
        </table>

        <div class="signature-section" style="font-size: __SIGNATURE_FONT_SIZE__px;">
            <table class="signature-table">
                <tr><td class="signature-label">Ditetapkan di</td><td class="colon">:</td><td>__SIGNATURE_CITY__</td></tr>
                <tr><td class="signature-label">__SIGNATURE_DATE_LABEL__</td><td class="colon">:</td><td>__SIGNATURE_DATE__</td></tr>
                <tr><td colspan="3" class="signature-body-top">__SIGNATURE_BODY_TOP__</td></tr>
                <tr><td colspan="3" class="signature-role">__SIGNATURE_ROLE__</td></tr>
                <tr><td colspan="3" class="signature-space"></td></tr>
                <tr><td colspan="3" class="signature-name">__SIGNATURE_NAME__</td></tr>
            </table>
        </div>

        <div class="footer-section">
            <div class="tembusan-block">
                <div style="font-size: __TEMBUSAN_TITLE_FONT_SIZE__px;">__TEMBUSAN_TITLE__</div>
                <ol style="font-size: __TEMBUSAN_ITEMS_FONT_SIZE__px;">
                    __TEMBUSAN_ITEMS__
                </ol>
            </div>
        </div>
    </div>
</div>
HTML, [
            '__LOGO_URL__' => $this->attributeValue($builderData['logo_url']),
            '__HEADER_TOPLINE__' => $this->formatBuilderText($builderData['header_topline']),
            '__HEADER_TITLE__' => $this->formatBuilderText($builderData['header_title']),
            '__HEADER_ADDRESS__' => $this->formatBuilderText($builderData['header_address']),
            '__HEADER_PHONE__' => $this->formatBuilderText($builderData['header_phone']),
            '__HEADER_EMAIL__' => $this->formatBuilderText($builderData['header_email']),
            '__HEADER_TOPLINE_FONT_SIZE__' => $this->fontSizeValue($builderData['header_topline_font_size'], 13),
            '__HEADER_TITLE_FONT_SIZE__' => $this->fontSizeValue($builderData['header_title_font_size'], 26),
            '__HEADER_ADDRESS_FONT_SIZE__' => $this->fontSizeValue($builderData['header_address_font_size'], 11),
            '__HEADER_CONTACT_FONT_SIZE__' => $this->fontSizeValue($builderData['header_contact_font_size'], 11),
            '__WHATSAPP_ICON_URL__' => $this->attributeValue($this->placeholderSvgDataUri('', '#16a34a', '#ffffff', 'whatsapp')),
            '__EMAIL_ICON_URL__' => $this->attributeValue($this->placeholderSvgDataUri('', '#0f766e', '#ffffff', 'email')),
            '__DECISION_TITLE__' => $this->formatBuilderText($builderData['decision_title']),
            '__NOMOR_SK__' => $this->formatBuilderText($builderData['nomor_sk']),
            '__OPENING_LINE__' => $this->formatBuilderText($builderData['opening_line']),
            '__DECISION_TITLE_FONT_SIZE__' => $this->fontSizeValue($builderData['decision_title_font_size'], 13),
            '__DECISION_NUMBER_FONT_SIZE__' => $this->fontSizeValue($builderData['decision_number_font_size'], 12),
            '__OPENING_LINE_FONT_SIZE__' => $this->fontSizeValue($builderData['opening_line_font_size'], 11.5),
            '__MENIMBANG_TEXT__' => $this->formatBuilderText($builderData['menimbang_text']),
            '__MENGINGAT_TEXT__' => $this->formatBuilderText($builderData['mengingat_text']),
            '__MEMPERHATIKAN_TEXT__' => $this->formatBuilderText($builderData['memperhatikan_text']),
            '__CONSIDERATION_FONT_SIZE__' => $this->fontSizeValue($builderData['consideration_font_size'], 11.5),
            '__MEMUTUSKAN_TITLE__' => $this->formatBuilderText($builderData['memutuskan_title']),
            '__PERTAMA_INTRO__' => $this->formatBuilderText($builderData['pertama_intro']),
            '__PERTAMA_PENUTUP__' => $this->formatBuilderText($builderData['pertama_penutup']),
            '__KEDUA_TEXT__' => $this->formatBuilderText($builderData['kedua_text']),
            '__KETIGA_TEXT__' => $this->formatBuilderText($builderData['ketiga_text']),
            '__MEMUTUSKAN_TITLE_FONT_SIZE__' => $this->fontSizeValue($builderData['memutuskan_title_font_size'], 15),
            '__DECISION_BODY_FONT_SIZE__' => $this->fontSizeValue($builderData['decision_body_font_size'], 11.5),
            '__IDENTITY_TABLE_FONT_SIZE__' => $this->fontSizeValue($builderData['identity_table_font_size'], 11.5),
            '__SIGNATURE_CITY__' => $this->formatBuilderText($builderData['signature_city']),
            '__SIGNATURE_DATE_LABEL__' => $this->formatBuilderText($builderData['signature_date_label']),
            '__SIGNATURE_DATE__' => $this->formatBuilderText($builderData['signature_date']),
            '__SIGNATURE_BODY_TOP__' => $this->formatBuilderText($builderData['signature_body_top']),
            '__SIGNATURE_ROLE__' => $this->formatBuilderText($builderData['signature_role']),
            '__SIGNATURE_NAME__' => $this->formatBuilderText($builderData['signature_name']),
            '__SIGNATURE_FONT_SIZE__' => $this->fontSizeValue($builderData['signature_font_size'], 11.5),
            '__TEMBUSAN_TITLE__' => $this->formatBuilderText($builderData['tembusan_title']),
            '__TEMBUSAN_ITEMS__' => $tembusanHtml,
            '__TEMBUSAN_TITLE_FONT_SIZE__' => $this->fontSizeValue($builderData['tembusan_title_font_size'], 11.5),
            '__TEMBUSAN_ITEMS_FONT_SIZE__' => $this->fontSizeValue($builderData['tembusan_items_font_size'], 11.5),
        ]);
    }

    protected function defaultCss(): string
    {
        return <<<'CSS'
body {
    font-family: "Cambria Math", Cambria, "Times New Roman", serif;
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
    text-align: right;
    vertical-align: middle;
}

.header-logo {
    width: 145px;
    height: 145px;
    object-fit: contain;
}

.header-topline {
    font-family: "Bodoni MT", Didot, "Times New Roman", serif;
    font-size: 13px;
    font-weight: 700;
    color: #159947;
    text-align: right;
    text-transform: uppercase;
}

.header-title {
    font-family: Calibri, Carlito, Arial, sans-serif;
    font-size: 26px;
    font-weight: 800;
    color: #0b8f2f;
    text-align: right;
    text-transform: uppercase;
    margin: 6px 0 4px;
}

.header-address {
    font-family: "Bodoni MT", Didot, "Times New Roman", serif;
    color: #159947;
    font-size: 11px;
    font-weight: 700;
    text-align: right;
}

.header-contact-table {
    margin-left: auto;
    margin-top: 2px;
    border-collapse: collapse;
}

.header-contact-table td {
    padding: 0;
    vertical-align: middle;
}

.header-contact-text {
    font-family: "Bodoni MT", Didot, "Times New Roman", serif;
    color: #159947;
    font-size: 11px;
    font-weight: 700;
    text-align: right;
    padding-right: 6px;
}

.header-contact-icon-cell {
    width: 16px;
    text-align: right;
}

.header-contact-icon {
    width: 12px;
    height: 12px;
    object-fit: contain;
}

.header-divider {
    border-top: 4px solid #0b8f2f;
    margin: 3px 0 4px;
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

.justified-text {
    text-align: justify;
    text-justify: inter-word;
    width: 100%;
}

.opening-line {
    margin: 0 0 10px;
}

.content-block {
    margin-top: 10px;
}

.consideration-table,
.decision-body-table {
    width: 100%;
    border-collapse: collapse;
}

.consideration-table td,
.decision-body-table td {
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
    margin-top: 18px;
}

.signature-table {
    width: 100%;
    border-collapse: collapse;
}

.signature-table td {
    padding: 0;
    vertical-align: top;
}

.signature-label {
    width: 105px;
}

.signature-body-top,
.signature-role,
.signature-name {
    text-align: left;
}

.signature-body-top {
    padding-top: 2px;
}

.signature-space {
    height: 62px;
}

.signature-name {
    font-weight: 700;
}

.footer-section {
    margin-top: 18px;
    width: 100%;
    overflow: hidden;
}

.tembusan-block {
    width: 100%;
}

.tembusan-block ol {
    margin: 4px 0 0 22px;
    padding: 0;
}

.tembusan-block li {
    margin-bottom: 2px;
}

.document::after {
    content: '';
    display: block;
    clear: both;
}
CSS;
    }

    protected function renderTemplate(SkTemplate $template, $user, array $overrides = []): string
    {
        return strtr((string) $template->content, $this->replacementMap($user, true, $overrides));
    }

    protected function renderText(string $text, $user, array $overrides = []): string
    {
        return trim(strtr($text, $this->replacementMap($user, false, $overrides)));
    }

    protected function replacementMap($user, bool $escapeHtml = true, array $overrides = []): array
    {
        Carbon::setLocale('id');

        $values = array_merge([
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
        ], $overrides);

        $map = [];
        foreach ($values as $key => $value) {
            $map['{{' . $key . '}}'] = $key === 'alamat_html'
                ? (string) $value
                : ($escapeHtml ? e((string) $value) : (string) $value);
        }

        return $map;
    }

    protected function previewSkNumberForUser($user): string
    {
        $setting = $this->resolveNumberSettingForPeriod((int) ($user->periode ?? 0));

        return $this->formatSkNumber(
            $setting,
            max((int) $setting->nomor_awal, (int) $setting->nomor_berikutnya),
            $user
        );
    }

    protected function resolveNumberSettingForPeriod(int $periodId, ?Collection $settings = null, bool $persistIfMissing = false): SkYayasanSetting
    {
        $existing = $settings?->get($periodId);
        if ($existing) {
            return $existing;
        }

        $periodName = DB::table('periode')->where('id', $periodId)->value('nama_periode') ?: 'UMUM';

        $setting = $persistIfMissing
            ? SkYayasanSetting::query()->firstOrCreate(
                ['periode_id' => $periodId],
                [
                    'nomor_pattern' => $this->defaultSkNumberPattern($periodName),
                    'nomor_awal' => 1,
                    'nomor_berikutnya' => 1,
                    'digit_nomor' => 4,
                    'is_active' => true,
                ]
            )
            : new SkYayasanSetting([
                'periode_id' => $periodId,
                'nomor_pattern' => $this->defaultSkNumberPattern($periodName),
                'nomor_awal' => 1,
                'nomor_berikutnya' => 1,
                'digit_nomor' => 4,
                'is_active' => true,
            ]);

        if ($settings) {
            $settings->put($periodId, $setting);
        }

        return $setting;
    }

    protected function formatSkNumber(SkYayasanSetting $setting, int $number, $user = null): string
    {
        $periodName = trim((string) ($user->nama_periode ?? DB::table('periode')->where('id', $setting->periode_id)->value('nama_periode') ?? 'UMUM'));
        $pattern = $setting->nomor_pattern ?: $this->defaultSkNumberPattern($periodName);
        $paddedNumber = str_pad((string) $number, max(1, (int) $setting->digit_nomor), '0', STR_PAD_LEFT);

        return strtr($pattern, [
            '{{nomor_urut}}' => $paddedNumber,
            '{{nomor_urut_raw}}' => (string) $number,
            '{{periode}}' => $periodName,
            '{{periode_upper}}' => strtoupper($periodName),
            '{{tahun}}' => now()->format('Y'),
            '{{bulan_romawi}}' => $this->romanMonth((int) now()->format('n')),
        ]);
    }

    protected function romanMonth(int $month): string
    {
        $map = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return $map[$month] ?? '';
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
            'whatsapp' => '
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64">
                    <circle cx="32" cy="32" r="30" fill="' . $primaryColor . '"/>
                    <path d="M21 18c-1 0-2 .5-2.8 1.7c-1.6 2.2-2.3 4.8-2 7.8c.6 6.7 5.2 13 11.5 17.5c5.7 4.1 12.1 5.6 16.6 4c2.3-.8 3.9-2.1 4.7-3.7l1.1-2.4c.4-.9 0-2-1-2.4l-5.8-2.4c-.8-.3-1.7 0-2.2.6l-2 2.6c-.5.6-1.3.8-2 .5c-2.8-1.2-6.2-4.2-8-6.7c-.5-.6-.5-1.4-.1-2l1.7-2.4c.4-.6.5-1.4.1-2l-3.2-6.1c-.4-.7-1.1-1.1-1.8-1.1z" fill="' . $secondaryColor . '"/>
                </svg>',
            'email' => '
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64">
                    <rect x="6" y="14" width="52" height="36" rx="6" fill="' . $primaryColor . '"/>
                    <path d="M12 22l20 14l20-14" fill="none" stroke="' . $secondaryColor . '" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 44l14-12M52 44L38 32" fill="none" stroke="' . $secondaryColor . '" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
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

    protected function splitLegacyHeaderContact(string $value): array
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            return ['', ''];
        }

        preg_match('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $trimmed, $emailMatch);
        preg_match('/(?:\+?\d[\d\s.-]{7,}\d)/', $trimmed, $phoneMatch);

        return [
            isset($phoneMatch[0]) ? trim($phoneMatch[0]) : '',
            isset($emailMatch[0]) ? trim($emailMatch[0]) : '',
        ];
    }

    protected function formatBuilderText(string $value): string
    {
        return nl2br($this->escapeTextPreservingPlaceholders($value));
    }

    protected function escapeTextPreservingPlaceholders(string $value): string
    {
        return preg_replace_callback('/\{\{[a-zA-Z0-9_]+\}\}/', function ($matches) use ($value) {
            return $matches[0];
        }, e($value));
    }

    protected function attributeValue(string $value): string
    {
        return e($value);
    }

    protected function fontSizeValue($value, float $default): string
    {
        $size = is_numeric($value) ? (float) $value : $default;
        $size = max(8, min(40, $size));

        return rtrim(rtrim(number_format($size, 1, '.', ''), '0'), '.');
    }
}
