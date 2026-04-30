@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border-slate-200 bg-white/90 shadow-sm focus:border-brand-400 focus:ring-2 focus:ring-brand-500/20']) }}>
