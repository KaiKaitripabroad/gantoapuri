<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">ログイン</h1>
        <p class="text-sm text-slate-500 mt-2">アカウントにサインイン</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="メールアドレス" class="!text-slate-700 !font-semibold" />
            <x-text-input id="email" class="input-field" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="パスワード" class="!text-slate-700 !font-semibold" />
            <x-text-input id="password" class="input-field" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded-md border-slate-300 text-brand-600 shadow-sm focus:ring-brand-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">ログイン状態を保持</span>
            </label>
        </div>

        @if (Route::has('password.request'))
            <div class="text-center">
                <a class="text-sm font-semibold text-brand-600 hover:text-brand-800" href="{{ route('password.request') }}">パスワードを忘れた方</a>
            </div>
        @endif

        <button type="submit" class="btn-primary w-full">ログイン</button>
    </form>

    <a href="{{ route('register') }}" class="btn-secondary mt-4 w-full font-semibold">
        新規登録はこちら
    </a>
</x-guest-layout>
