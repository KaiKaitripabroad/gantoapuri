<x-app-layout bottom-nav-active="home">
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">グループ作成</h1>
            <p class="text-xs text-slate-500 mt-0.5">名前・期日・招待メール</p>
        </div>
    </x-slot>

    <div class="card-surface p-5 sm:p-6">
        <form method="POST" action="{{ route('groups.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700">グループ名</label>
                <input id="name" name="name" type="text" required value="{{ old('name') }}" class="input-field" />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <div>
                <label for="deadline" class="block text-sm font-semibold text-slate-700">作成期日（ガントの逆算起点）</label>
                <input id="deadline" name="deadline" type="date" required value="{{ old('deadline') }}" class="input-field" />
                <x-input-error :messages="$errors->get('deadline')" class="mt-1" />
            </div>

            <div>
                <label for="invite_emails" class="block text-sm font-semibold text-slate-700">招待メール（カンマ・改行区切り）</label>
                <textarea id="invite_emails" name="invite_emails" rows="3" placeholder="member@example.com" class="input-field">{{ old('invite_emails') }}</textarea>
                <p class="mt-1.5 text-xs text-slate-500 leading-relaxed">保存後、グループ詳細から招待リンクをコピーして共有できます。</p>
            </div>

            <button type="submit" class="btn-primary w-full">グループを作成</button>
        </form>
    </div>
</x-app-layout>
