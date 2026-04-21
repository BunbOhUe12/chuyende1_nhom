@extends('layouts.app')

@section('fullwidth-hero')
{{-- Hero banner thu cũ đổi mới --}}
<section class="relative w-full overflow-hidden bg-slate-900" style="height: clamp(320px, 50vw, 560px);">
    <div class="absolute inset-0 z-0">
        <div class="h-full w-full bg-cover bg-center" style="background-image: url('{{ asset('images/bg123.png') }}');"></div>
    </div>
    <div class="pointer-events-none absolute inset-0 z-[1] bg-gradient-to-r from-slate-900/85 via-slate-900/50 to-transparent"></div>
    <div class="relative z-10 mx-auto flex h-full w-full max-w-[1600px] flex-col justify-center px-8 md:px-16">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-brand-light md:text-sm">Đại lý chính hãng</p>
        <h1 class="mt-2 text-4xl font-extrabold leading-[1.35] tracking-tight text-white drop-shadow md:text-6xl md:leading-[1.3]">
            THU CŨ<br><span class="text-brand">ĐỔI MỚI</span>
        </h1>
        <p class="mt-3 max-w-lg text-sm text-slate-200 md:text-base leading-relaxed">
            Mang xe cũ đến — nhận ngay ưu đãi lên đến <strong class="text-white">15%</strong> khi mua xe mới.<br>
            Thủ tục đơn giản, định giá minh bạch, hoàn tất trong ngày.
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="#dang-ky"
               class="inline-flex items-center gap-2 rounded-full bg-brand px-6 py-3 text-sm font-bold text-white shadow-lg shadow-brand/40 transition hover:bg-brand-dark">
                Đăng ký ngay
            </a>
            <a href="#quy-trinh"
               class="inline-flex items-center gap-2 rounded-full border border-white/50 bg-white/10 px-6 py-3 text-sm font-bold text-white backdrop-blur transition hover:bg-white/20">
                Xem quy trình
            </a>
        </div>
    </div>
</section>
@endsection

@section('content')

{{-- Nổi bật 3 điểm --}}
<div class="grid gap-4 sm:grid-cols-3 -mt-8 relative z-10">
    <div class="flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-md">
        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-brand/10 text-brand">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <h3 class="font-bold text-slate-900">Định giá minh bạch</h3>
            <p class="mt-1 text-sm text-slate-500">Định giá theo bảng giá thị trường, không ép giá, công khai từng khoản khấu trừ.</p>
        </div>
    </div>
    <div class="flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-md">
        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <h3 class="font-bold text-slate-900">Thủ tục nhanh – hoàn tất trong ngày</h3>
            <p class="mt-1 text-sm text-slate-500">Chỉ cần mang giấy tờ xe và CCCD, nhân viên hỗ trợ toàn bộ thủ tục sang tên.</p>
        </div>
    </div>
    <div class="flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-md">
        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
            </svg>
        </div>
        <div>
            <h3 class="font-bold text-slate-900">Ưu đãi thêm khi mua xe mới</h3>
            <p class="mt-1 text-sm text-slate-500">Nhận thêm phụ kiện, bảo hành mở rộng hoặc giảm tiền mặt khi đổi sang xe Honda chính hãng.</p>
        </div>
    </div>
</div>

{{-- Quy trình --}}
<section id="quy-trinh" class="mt-14 scroll-mt-20">
    <div class="mb-2 text-center">
        <span class="rounded-full bg-brand/10 px-3 py-1 text-xs font-bold uppercase tracking-widest text-brand">Quy trình</span>
    </div>
    <h2 class="text-center text-2xl font-extrabold text-slate-900 md:text-3xl">4 bước đơn giản để đổi xe mới</h2>
    <p class="mt-2 text-center text-sm text-slate-500">Từ khi đăng ký đến khi lấy xe mới, chỉ mất 1 buổi làm việc.</p>

    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['step'=>'01','icon'=>'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z','title'=>'Đăng ký & Hẹn lịch','desc'=>'Điền form trực tuyến hoặc gọi điện để hẹn ngày mang xe đến. Nhân viên xác nhận trong vòng 30 phút.'],
            ['step'=>'02','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01','title'=>'Kiểm tra & Định giá xe cũ','desc'=>'Kỹ thuật viên kiểm tra toàn diện: khung, máy, ngoại thất. Báo giá thu mua công khai, tham khảo bảng giá thị trường.'],
            ['step'=>'03','icon'=>'M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z','title'=>'Chọn xe mới & Thanh toán','desc'=>'Chọn mẫu xe Honda ưng ý, áp dụng chiết khấu thu cũ đổi mới. Hỗ trợ vay trả góp lãi suất 0% (điều kiện áp dụng).'],
            ['step'=>'04','icon'=>'M5 13l4 4L19 7','title'=>'Nhận xe & Hoàn tất giấy tờ','desc'=>'Ký hợp đồng, hoàn tất thủ tục sang tên xe cũ và đăng ký xe mới. Nhận xe ngay trong ngày hoặc theo lịch hẹn.'],
        ] as $s)
        <div class="relative flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="absolute -top-4 left-6 flex h-8 w-8 items-center justify-center rounded-full bg-brand text-xs font-extrabold text-white shadow">{{ $s['step'] }}</div>
            <div class="mt-2 flex h-12 w-12 items-center justify-center rounded-xl bg-brand/10 text-brand">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}"/>
                </svg>
            </div>
            <h3 class="mt-3 font-bold text-slate-900">{{ $s['title'] }}</h3>
            <p class="mt-2 text-sm text-slate-500 leading-relaxed">{{ $s['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- Điều kiện xe thu cũ --}}
<section class="mt-14">
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm">
            <h2 class="text-xl font-extrabold text-slate-900">Điều kiện xe được thu cũ</h2>
            <p class="mt-1 text-sm text-slate-500">Xe cần đáp ứng các tiêu chí sau để được định giá thu mua.</p>
            <ul class="mt-5 space-y-3 text-sm">
                @foreach ([
                    'Xe máy các loại, xe tay ga, xe số, xe côn tay thuộc mọi hãng sản xuất.',
                    'Xe còn hoạt động, máy nổ, không bị mất trộm hay tranh chấp pháp lý.',
                    'Có đầy đủ giấy tờ gốc: đăng ký xe (cà vẹt/giấy hồng).',
                    'Chủ xe có CCCD/CMND hợp lệ, tên khớp với giấy đăng ký.',
                    'Xe không bị biến dạng khung sườn nghiêm trọng, không bị ngập nước toàn bộ.',
                ] as $item)
                <li class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 flex-shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-slate-700">{{ $item }}</span>
                </li>
                @endforeach
            </ul>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-7 shadow-sm">
            <h2 class="text-xl font-extrabold text-slate-900">Giấy tờ cần mang theo</h2>
            <p class="mt-1 text-sm text-slate-500">Chuẩn bị đầy đủ để hoàn tất trong một lần đến.</p>
            <ul class="mt-5 space-y-3 text-sm">
                @foreach ([
                    ['doc'=>'Đăng ký xe (cà vẹt/giấy hồng)', 'note'=>'Bản gốc — bắt buộc'],
                    ['doc'=>'CCCD / CMND', 'note'=>'Bản gốc — bắt buộc'],
                    ['doc'=>'Bảo hiểm xe máy', 'note'=>'Nếu còn hiệu lực'],
                    ['doc'=>'Sổ bảo hành (nếu có)', 'note'=>'Tăng giá trị định giá'],
                    ['doc'=>'Chìa khóa xe (cả chìa phụ nếu có)', 'note'=>'Tăng giá trị định giá'],
                ] as $d)
                <li class="flex items-start justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="font-medium text-slate-800">{{ $d['doc'] }}</span>
                    </div>
                    <span class="flex-shrink-0 rounded-full {{ str_contains($d['note'],'bắt buộc') ? 'bg-rose-100 text-rose-700' : 'bg-slate-200 text-slate-500' }} px-2 py-0.5 text-xs">{{ $d['note'] }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

{{-- Yếu tố ảnh hưởng định giá --}}
<section class="mt-10 rounded-2xl border border-brand/20 bg-gradient-to-br from-brand/5 to-orange-50 p-7">
    <h2 class="text-xl font-extrabold text-slate-900">Các yếu tố ảnh hưởng đến giá thu mua</h2>
    <p class="mt-1 text-sm text-slate-500">Định giá dựa trên các tiêu chí khách quan, có thể điều chỉnh tuỳ từng xe.</p>
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['label'=>'Năm sản xuất','desc'=>'Xe càng mới, giá càng cao. Xe dưới 3 năm tuổi được định giá tốt nhất.','icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['label'=>'Số km đã đi','desc'=>'Ít km sử dụng, máy móc ít hao mòn, giá thu mua cao hơn.','icon'=>'M13 10V3L4 14h7v7l9-11h-7z'],
            ['label'=>'Tình trạng ngoại thất','desc'=>'Xe không trầy xước, không va chạm lớn sẽ được cộng thêm điểm định giá.','icon'=>'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['label'=>'Thương hiệu & Model','desc'=>'Honda, Yamaha, SYM… định giá theo nhu cầu thị trường thực tế từng thời điểm.','icon'=>'M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18'],
        ] as $f)
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand/10 text-brand">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/>
                </svg>
            </div>
            <h3 class="mt-3 font-bold text-slate-900 text-sm">{{ $f['label'] }}</h3>
            <p class="mt-1 text-xs text-slate-500 leading-relaxed">{{ $f['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- Ưu đãi khi đổi xe --}}
<section class="mt-10">
    <h2 class="text-xl font-extrabold text-slate-900 md:text-2xl">Ưu đãi dành cho khách đổi xe</h2>
    <div class="mt-5 grid gap-4 sm:grid-cols-3">
        <div class="relative overflow-hidden rounded-2xl bg-brand p-6 text-white shadow-lg">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="text-3xl font-extrabold">Đến 15%</div>
            <div class="mt-1 text-sm font-semibold text-orange-100">Chiết khấu giá xe mới</div>
            <p class="mt-2 text-xs text-white/80 leading-relaxed">Áp dụng khi mua xe Honda chính hãng trong cùng ngày đổi xe cũ.</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-slate-800 p-6 text-white shadow-lg">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="text-3xl font-extrabold">Miễn phí</div>
            <div class="mt-1 text-sm font-semibold text-slate-300">Phí sang tên &amp; đăng ký</div>
            <p class="mt-2 text-xs text-white/80 leading-relaxed">Cửa hàng hỗ trợ toàn bộ thủ tục sang tên xe cũ và đăng ký xe mới.</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-emerald-600 p-6 text-white shadow-lg">
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="text-3xl font-extrabold">Tặng thêm</div>
            <div class="mt-1 text-sm font-semibold text-emerald-100">Phụ kiện chính hãng</div>
            <p class="mt-2 text-xs text-white/80 leading-relaxed">Mũ bảo hiểm Honda, áo mưa cao cấp hoặc bình xịt dưỡng xe (tuỳ chương trình).</p>
        </div>
    </div>
</section>

{{-- Xe mới đang bán --}}
@if ($newModels->isNotEmpty())
<section class="mt-14">
    <div class="mb-5 flex items-end justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 md:text-2xl">Xe mới đang có tại cửa hàng</h2>
            <p class="mt-1 text-sm text-slate-500">Chọn mẫu xe bạn muốn đổi sang ngay hôm nay.</p>
        </div>
        <a href="{{ route('shop') }}" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-semibold text-brand hover:bg-slate-50">Xem tất cả</a>
    </div>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($newModels as $m)
            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:shadow-md">
                <div class="aspect-[4/3] overflow-hidden rounded-xl bg-slate-100">
                    <img src="{{ \App\Support\MotoImage::cardUrl($m) }}" class="h-full w-full object-cover" alt="{{ $m->name }}"
                         onerror="this.onerror=null;this.src={{ json_encode(\App\Support\MotoImage::fallbackUrl()) }}" />
                </div>
                <div class="mt-3 text-xs font-semibold uppercase tracking-wide text-brand">{{ $m->brand->name ?? '' }} · {{ $m->category->name ?? '' }}</div>
                <a href="{{ route('motorcycles.show', $m) }}" class="mt-1 block font-semibold text-slate-900 hover:text-brand">{{ $m->name }}</a>
                <div class="mt-1 text-sm font-bold text-brand">{{ number_format((float) $m->price) }}đ</div>
                <a href="{{ route('motorcycles.show', $m) }}"
                   class="mt-3 block w-full rounded-xl border border-brand/40 py-2 text-center text-sm font-bold text-brand hover:bg-brand hover:text-white transition">
                    Xem chi tiết
                </a>
            </article>
        @endforeach
    </div>
</section>
@endif

{{-- FAQ --}}
<section class="mt-14">
    <h2 class="text-xl font-extrabold text-slate-900 md:text-2xl">Câu hỏi thường gặp</h2>
    <div class="mt-5 space-y-3" id="faq-list">
        @foreach ([
            ['q'=>'Xe của tôi bị trầy xước nhẹ có được thu cũ không?','a'=>'Có. Xe trầy xước nhẹ vẫn được thu cũ và định giá bình thường. Chúng tôi sẽ khấu trừ một phần nhỏ vào giá trị định giá tùy mức độ hư hỏng, và thông báo rõ ràng trước khi bạn quyết định.'],
            ['q'=>'Xe không có giấy tờ gốc có đổi được không?','a'=>'Rất tiếc, xe cần có đăng ký xe (giấy hồng/cà vẹt) bản gốc và CCCD của chủ xe mới thực hiện được thủ tục sang tên hợp pháp. Nếu xe đang thế chấp ngân hàng, vui lòng liên hệ ngân hàng để giải chấp trước.'],
            ['q'=>'Tôi có thể mang xe của người thân đến đổi không?','a'=>'Có, nhưng cần có giấy ủy quyền công chứng từ chủ xe (tên trên giấy đăng ký) cùng bản sao CCCD của chủ xe và người được ủy quyền.'],
            ['q'=>'Thời gian định giá mất bao lâu?','a'=>'Thông thường 15–30 phút nếu xe đã được dọn sạch và mang đầy đủ giấy tờ. Bạn có thể đặt lịch trước để nhân viên chuẩn bị sẵn.'],
            ['q'=>'Tôi có thể thương lượng giá thu mua không?','a'=>'Giá định giá dựa trên bảng giá thị trường khách quan. Bạn có thể trao đổi với nhân viên nếu chưa đồng ý với mức giá đề xuất. Chúng tôi luôn cố gắng đưa ra mức giá hợp lý nhất cho cả hai bên.'],
            ['q'=>'Nếu tôi chỉ muốn bán xe cũ mà không mua xe mới có được không?','a'=>'Chương trình "Thu cũ đổi mới" áp dụng kèm điều kiện mua xe mới tại cửa hàng. Nếu bạn chỉ muốn bán xe, vui lòng liên hệ trực tiếp để được tư vấn phương án phù hợp.'],
        ] as $i => $faq)
        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
            <button type="button"
                    onclick="toggleFaq({{ $i }})"
                    class="flex w-full items-center justify-between px-5 py-4 text-left text-sm font-semibold text-slate-900 hover:bg-slate-50 transition">
                <span>{{ $faq['q'] }}</span>
                <svg id="faq-icon-{{ $i }}" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 text-slate-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="faq-body-{{ $i }}" class="hidden border-t border-slate-100 px-5 py-4 text-sm text-slate-600 leading-relaxed">
                {{ $faq['a'] }}
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- Form đăng ký --}}
<section id="dang-ky" class="mt-14 scroll-mt-20">
    <div class="overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 shadow-xl">
        <div class="grid lg:grid-cols-2">
            {{-- Left: Info --}}
            <div class="p-8 md:p-12 text-white">
                <span class="rounded-full bg-brand/20 px-3 py-1 text-xs font-bold uppercase tracking-widest text-brand-light">Đăng ký ngay</span>
                <h2 class="mt-4 text-2xl font-extrabold leading-snug md:text-3xl">
                    Để lại thông tin<br>nhận tư vấn miễn phí
                </h2>
                <p class="mt-3 text-sm text-slate-300 leading-relaxed">
                    Nhân viên sẽ liên hệ trong vòng <strong class="text-white">30 phút</strong> để báo giá sơ bộ và hẹn lịch kiểm tra xe.
                </p>

                <div class="mt-8 space-y-4 text-sm">
                    <div class="flex items-center gap-3 text-slate-300">
                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-brand/20 text-brand">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        Hotline: <a href="tel:0909123456" class="text-white font-semibold hover:underline">0909 123 456</a>
                    </div>
                    <div class="flex items-center gap-3 text-slate-300">
                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-brand/20 text-brand">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        Thứ 2 – Chủ nhật: 7:30 – 18:00
                    </div>
                </div>
            </div>

            {{-- Right: Form --}}
            <div class="bg-white p-8 md:p-12">
                <form id="trade-in-form" action="{{ route('consultation.public') }}" method="post" class="space-y-4">
                    @csrf
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide">Họ và tên *</label>
                            <input name="full_name" required placeholder="Nguyễn Văn A"
                                   class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-brand focus:outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide">Số điện thoại *</label>
                            <input name="phone" required type="tel" placeholder="0909 xxx xxx"
                                   class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-brand focus:outline-none" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide">Xe cũ của bạn</label>
                        <input name="product" placeholder="VD: Honda Wave Alpha 2019, màu đen"
                               class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-brand focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide">Xe mới muốn đổi sang</label>
                        <input name="area" placeholder="VD: Honda Vision, Honda SH 160i..."
                               class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-brand focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wide">Ghi chú thêm</label>
                        <textarea name="note" rows="3" placeholder="Tình trạng xe, số km, khu vực của bạn..."
                                  class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm focus:border-brand focus:outline-none"></textarea>
                    </div>

                    <div id="trade-in-msg" class="hidden rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"></div>

                    <button type="submit"
                            class="w-full rounded-xl bg-brand py-3.5 text-sm font-extrabold uppercase tracking-wider text-white shadow-lg transition hover:bg-brand-dark active:scale-95">
                        Gửi yêu cầu tư vấn miễn phí
                    </button>
                    <p class="text-center text-xs text-slate-400">Thông tin của bạn được bảo mật hoàn toàn.</p>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    function toggleFaq(i) {
        var body = document.getElementById('faq-body-' + i);
        var icon = document.getElementById('faq-icon-' + i);
        var isOpen = !body.classList.contains('hidden');
        body.classList.toggle('hidden', isOpen);
        icon.style.transform = isOpen ? '' : 'rotate(180deg)';
    }

    (function () {
        var form = document.getElementById('trade-in-form');
        var msgBox = document.getElementById('trade-in-msg');
        if (!form) return;
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = form.querySelector('button[type=submit]');
            btn.disabled = true;
            btn.textContent = 'Đang gửi...';
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: new FormData(form)
            }).then(function (r) { return r.json(); }).then(function (d) {
                if (d.success) {
                    form.reset();
                    msgBox.textContent = 'Cảm ơn! Nhân viên sẽ liên hệ với bạn trong vòng 30 phút.';
                    msgBox.classList.remove('hidden');
                    msgBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }).catch(function () {}).finally(function () {
                btn.disabled = false;
                btn.textContent = 'Gửi yêu cầu tư vấn miễn phí';
            });
        });
    })();
</script>
@endpush
