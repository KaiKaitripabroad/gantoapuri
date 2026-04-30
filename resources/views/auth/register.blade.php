<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">新規登録</h1>
        <p class="text-sm text-slate-500 mt-2">アカウントを作成</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" value="名前" class="!text-slate-700 !font-semibold" />
            <x-text-input id="name" class="input-field" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="メールアドレス" class="!text-slate-700 !font-semibold" />
            <x-text-input id="email" class="input-field" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="パスワード" class="!text-slate-700 !font-semibold" />
            <x-text-input id="password" class="input-field" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="パスワード（確認）" class="!text-slate-700 !font-semibold" />
            <x-text-input id="password_confirmation" class="input-field" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="btn-primary w-full">アカウント作成</button>
    </form>

    <a href="{{ route('login') }}" class="btn-secondary mt-4 w-full font-semibold">
        ログインはこちら
    </a>
</x-guest-layout>
