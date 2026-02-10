<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            Isi Assessment - {{ $assessment->unit_name }} ({{ $assessment->accreditationYear->year }})
        </h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
        @if(session('success'))
        <div class="p-3 bg-green-100 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white p-6 rounded shadow">
            <form method="POST" action="{{ route('assessor.assessments.fill.update', $assessment) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                @foreach($questions as $q)
                @php
                $ans = $answers[$q->id] ?? null;
                $name = "q_{$q->id}";
                $valText = $ans?->value_text;
                $valJson = $ans?->value_json ?? [];
                @endphp

                <div class="border rounded p-4">
                    <div class="font-semibold">
                        {{ $q->standard?->code ? $q->standard->code . ' - ' : '' }}{{ $q->label }}
                        @if($q->is_required) <span class="text-red-600">*</span> @endif
                    </div>

                    <div class="mt-2">
                        @switch($q->type)
                        @case('text')
                        <input name="{{ $name }}" value="{{ old($name, $valText) }}" class="w-full border rounded p-2" />
                        @break

                        @case('textarea')
                        <textarea name="{{ $name }}" class="w-full border rounded p-2" rows="4">{{ old($name, $valText) }}</textarea>
                        @break

                        @case('number')
                        <input type="number" name="{{ $name }}" value="{{ old($name, $valText) }}" class="w-full border rounded p-2" />
                        @break

                        @case('select')
                        <select name="{{ $name }}" class="w-full border rounded p-2">
                            <option value="">-- pilih --</option>
                            @foreach($q->options as $opt)
                            <option value="{{ $opt->value }}" @selected(old($name, $valText)==$opt->value)>{{ $opt->label }}</option>
                            @endforeach
                        </select>
                        @break

                        @case('radio')
                        <div class="space-y-2">
                            @foreach($q->options as $opt)
                            <label class="flex items-center gap-2">
                                <input type="radio" name="{{ $name }}" value="{{ $opt->value }}"
                                    @checked(old($name, $valText)==$opt->value) />
                                {{ $opt->label }}
                            </label>
                            @endforeach
                        </div>
                        @break

                        @case('checkbox')
                        <div class="space-y-2">
                            @foreach($q->options as $opt)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="{{ $name }}[]" value="{{ $opt->value }}"
                                    @checked(in_array($opt->value, old($name, $valJson))) />
                                {{ $opt->label }}
                            </label>
                            @endforeach
                        </div>
                        @break

                        @case('file')
                        <input type="file" name="{{ $name }}" class="w-full border rounded p-2" />
                        @if($ans?->file_path)
                        <div class="text-sm mt-2">
                            File saat ini: <a class="underline" href="{{ asset('storage/'.$ans->file_path) }}" target="_blank">lihat</a>
                        </div>
                        @endif
                        @break
                        @endswitch

                        @error($name)
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                @endforeach

                <div class="flex gap-3">
                    <button class="px-4 py-2 bg-gray-800 text-white rounded" type="submit">Simpan Draft</button>

                    <button class="px-4 py-2 bg-black text-white rounded"
                        type="submit" name="submit" value="1"
                        onclick="return confirm('Submit assessment?')">
                        Submit
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-semibold mb-4">Temuan / Kritik (PTK Otomatis)</h3>

            <form method="POST" action="{{ route('assessor.findings.store', $assessment) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block mb-1">Standar</label>
                    <select name="standard_id" class="w-full border rounded p-2" required>
                        @foreach($standards as $s)
                        <option value="{{ $s->id }}">{{ $s->code }} - {{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-1">Area Audit</label>
                    <select name="audit_area_id" class="w-full border rounded p-2" required>
                        @foreach($areas as $a)
                        <option value="{{ $a->id }}">{{ $a->name }} ({{ $a->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block mb-1">Judul Temuan</label>
                    <input name="title" class="w-full border rounded p-2" required />
                </div>

                <div class="md:col-span-2">
                    <label class="block mb-1">Deskripsi</label>
                    <textarea name="description" class="w-full border rounded p-2" rows="3"></textarea>
                </div>

                <div>
                    <label class="block mb-1">Severity</label>
                    <select name="severity" class="w-full border rounded p-2" required>
                        <option value="minor">minor</option>
                        <option value="major">major</option>
                        <option value="critical">critical</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button class="px-4 py-2 bg-black text-white rounded">Tambah Temuan</button>
                </div>
            </form>

            <div class="mt-6">
                <h4 class="font-semibold mb-2">List Temuan</h4>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-2 text-left">Kode</th>
                            <th class="p-2 text-left">Standar</th>
                            <th class="p-2 text-left">Area</th>
                            <th class="p-2 text-left">Judul</th>
                            <th class="p-2 text-left">Severity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assessment->findings as $f)
                        <tr class="border-t">
                            <td class="p-2">{{ $f->code }}</td>
                            <td class="p-2">{{ $f->standard->code }}</td>
                            <td class="p-2">{{ $f->auditAreaNames }}</td>
                            <td class="p-2">{{ $f->title }}</td>
                            <td class="p-2">{{ $f->severity }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>