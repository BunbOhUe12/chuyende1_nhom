@extends('layouts.app')

@section('content')
    @php($s = config('store'))

    <div class="overflow-hidden rounded-2xl bg-gradient-to-br from-slate-100 via-white to-slate-100 p-[1px] shadow-lg shadow-slate-900/5 ring-1 ring-slate-200/80">
        <div class="relative rounded-[1.05rem] bg-gradient-to-br from-slate-50 to-slate-100 px-6 py-12 text-slate-900 md:px-12">
            <p class="text-sm font-semibold uppercase tracking-widest text-brand">Thông tin cửa hàng</p>
            <h1 class="mt-2 text-3xl font-bold md:text-4xl">{{ $s['name'] }}</h1>
            <p class="mt-3 max-w-2xl text-lg text-slate-600">{{ $s['tagline'] }}</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('shop') }}" class="rounded-xl bg-brand px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-brand/25 hover:bg-brand-dark">Xem xe và phụ tùng</a>
                <a href="tel:{{ preg_replace('/\s+/', '', $s['hotline']) }}" class="rounded-xl border-2 border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-800 hover:border-brand/40 hover:text-brand">Gọi hotline</a>
            </div>
        </div>
    </div>

    <section id="ban-xe" class="mt-10 rounded-2xl border border-slate-200 bg-white p-6 shadow-md md:p-8">
        <h2 class="text-xl font-bold text-brand">Bán xe máy và phụ tùng</h2>
        <p class="mt-3 max-w-3xl text-slate-600 leading-relaxed">
            Nhận thu mua, ký gửi xe và phụ tùng còn mới. Hỗ trợ định giá nhanh, thủ tục rõ ràng, thanh toán an toàn.
        </p>
        <div class="mt-5 flex flex-wrap gap-3">
            <a href="tel:{{ preg_replace('/\s+/', '', $s['hotline']) }}" class="inline-flex items-center rounded-xl bg-brand px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-brand-dark">Gọi {{ $s['hotline'] }}</a>
            <a href="{{ route('shop', ['type' => 'phu-tung']) }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-50 px-5 py-2.5 text-sm font-bold text-slate-800 hover:border-brand/40 hover:text-brand">Xem phụ tùng</a>
        </div>
    </section>
@endsection

