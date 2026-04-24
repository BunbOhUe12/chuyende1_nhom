@extends('layouts.app')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-2xl border bg-white p-6">
            <h1 class="text-xl font-semibold">Thanh toán</h1>

            <form id="checkout-form" action="{{ route('checkout.place') }}" method="post" class="mt-6 space-y-4">
                @csrf
                @foreach($items as $it)
                    <input type="hidden" name="checkout_item_ids[]" value="{{ (int) $it['motorcycle_id'] }}"/>
                @endforeach
                <input type="hidden" name="payment_method" id="payment_method_input" value="{{ old('payment_method') }}"/>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm text-slate-600">Họ tên</label>
                        <input name="full_name" required value="{{ old('full_name', $user?->name) }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"/>
                        @error('full_name')<div class="mt-1 text-sm text-rose-600">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block text-sm text-slate-600">Số điện thoại</label>
                        <input name="phone" required value="{{ old('phone') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"/>
                        @error('phone')<div class="mt-1 text-sm text-rose-600">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-slate-600">Địa chỉ</label>
                    <input name="address" value="{{ old('address') }}" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"/>
                    @error('address')<div class="mt-1 text-sm text-rose-600">{{ $message }}</div>@enderror
                </div>

                <p class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                    Sau khi nhấn <strong>Đặt hàng</strong>, bạn sẽ chọn <strong>thanh toán khi nhận hàng (COD)</strong> hoặc <strong>VNPAY</strong> trong bước tiếp theo.
                </p>

                <div>
                    <label class="block text-sm text-slate-600">Ghi chú</label>
                    <textarea name="notes" rows="4" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('notes') }}</textarea>
                    @error('notes')<div class="mt-1 text-sm text-rose-600">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="block text-sm text-slate-600">Voucher / Mã giảm giá</label>
                    <select name="promotion_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <option value="">-- Không áp dụng voucher --</option>
                        @foreach($promotions as $promo)
                            <option value="{{ $promo->id }}" @selected((string) old('promotion_id') === (string) $promo->id)>
                                {{ $promo->code }} - {{ $promo->name }}
                                ({{ $promo->type === 'percent' ? ('-' . rtrim(rtrim(number_format((float) $promo->value, 2), '0'), '.') . '%') : ('-' . number_format((float) $promo->value) . 'đ') }})
                            </option>
                        @endforeach
                    </select>
                    @error('promotion_id')<div class="mt-1 text-sm text-rose-600">{{ $message }}</div>@enderror
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <h2 class="text-sm font-semibold text-slate-900">Điều khoản thanh toán</h2>
                    <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-slate-700">
                        <li><strong>COD:</strong> Thanh toán khi nhận hàng — nhân viên có thể liên hệ xác nhận trước khi giao.</li>
                        <li><strong>VNPAY:</strong> Thanh toán trực tuyến qua cổng VNPAY (sau khi đặt hàng, làm theo hướng dẫn trên trang xác nhận).</li>
                        <li>Kho được trừ theo đơn hợp lệ sau khi xác nhận.</li>
                    </ul>
                    <label class="mt-3 flex items-start gap-2 text-sm text-slate-800">
                        <input
                            id="accept_terms"
                            type="checkbox"
                            name="accept_terms"
                            value="1"
                            class="mt-0.5 rounded border-slate-300"
                            @checked(old('accept_terms'))
                        />
                        <span>Tôi đã đọc kỹ và đồng ý với điều khoản thanh toán.</span>
                    </label>
                    @error('accept_terms')<div class="mt-2 text-sm text-rose-600">{{ $message }}</div>@enderror
                </div>

                @error('payment_method')<div class="text-sm text-rose-600">{{ $message }}</div>@enderror

                <button type="button" id="open-payment-modal-btn" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white disabled:cursor-not-allowed disabled:opacity-50" @disabled(!old('accept_terms'))>
                    Đặt hàng
                </button>
            </form>
        </div>

        <aside class="rounded-2xl border bg-white p-6">
            <h2 class="font-semibold">Đơn hàng</h2>
            <div class="mt-4 space-y-3 text-sm">
                @foreach($items as $it)
                    <div class="flex justify-between gap-4">
                        <div class="min-w-0">
                            <div class="truncate font-medium">{{ $it['name'] }}</div>
                            <div class="text-slate-600">{{ $it['quantity'] }} × {{ number_format((float)$it['price']) }}₫</div>
                            @if(!empty($it['selected_color']))
                                <div class="text-xs text-slate-500">Màu: {{ $it['selected_color'] }}</div>
                            @endif
                        </div>
                        <div class="font-medium">{{ number_format((float)$it['line_total']) }}₫</div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 border-t pt-4 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-600">Tạm tính</span>
                    <span class="font-semibold">{{ number_format((float)$subtotal) }}₫</span>
                </div>
                <div class="mt-2 flex justify-between">
                    <span class="text-slate-600">Giảm giá voucher</span>
                    <span class="font-medium">Sẽ áp dụng khi đặt hàng</span>
                </div>
                <div class="mt-2 flex justify-between">
                    <span class="text-slate-600">Tổng</span>
                    <span class="font-semibold">{{ number_format((float)$subtotal) }}₫</span>
                </div>
            </div>
        </aside>
    </div>

    {{-- Modal chọn phương thức thanh toán --}}
    <div id="payment-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" aria-modal="true" role="dialog">
        <div class="relative w-full max-w-md rounded-2xl border bg-white p-6 shadow-xl">
            <h2 class="text-lg font-semibold text-slate-900">Chọn phương thức thanh toán</h2>
            <p class="mt-1 text-sm text-slate-600">Vui lòng chọn một trong hai hình thức bên dưới để hoàn tất đặt hàng.</p>

            <div class="mt-6 grid gap-3">
                <button type="button" id="pay-cod-btn" class="flex w-full flex-col items-start rounded-xl border-2 border-slate-200 bg-white px-4 py-4 text-left transition hover:border-brand hover:bg-brand-muted">
                    <span class="font-semibold text-slate-900">Thanh toán khi nhận hàng (COD)</span>
                    <span class="mt-1 text-sm text-slate-600">Trả tiền khi nhận xe / phụ tùng theo thỏa thuận giao hàng.</span>
                </button>
                <button type="button" id="pay-vnpay-btn" class="flex w-full flex-col items-start rounded-xl border-2 border-slate-200 bg-white px-4 py-4 text-left transition hover:border-brand hover:bg-brand-muted">
                    <span class="font-semibold text-slate-900">VNPAY</span>
                    <span class="mt-1 text-sm text-slate-600">Thanh toán trực tuyến bằng QR / thẻ / tài khoản qua cổng VNPAY.</span>
                </button>
            </div>

            <button type="button" id="close-payment-modal" class="mt-4 w-full rounded-lg border border-slate-200 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Đóng
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var form = document.getElementById('checkout-form');
            var terms = document.getElementById('accept_terms');
            var openBtn = document.getElementById('open-payment-modal-btn');
            var modal = document.getElementById('payment-modal');
            var closeBtn = document.getElementById('close-payment-modal');
            var payCod = document.getElementById('pay-cod-btn');
            var payVnpay = document.getElementById('pay-vnpay-btn');
            var paymentInput = document.getElementById('payment_method_input');

            function refreshSubmitState() {
                if (!terms || !openBtn) return;
                openBtn.disabled = !terms.checked;
            }

            function openModal() {
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                if (!terms || !terms.checked) {
                    return;
                }
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            function submitWithMethod(method) {
                paymentInput.value = method;
                form.submit();
            }

            if (terms) {
                terms.addEventListener('change', refreshSubmitState);
                refreshSubmitState();
            }

            if (openBtn) {
                openBtn.addEventListener('click', openModal);
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', closeModal);
            }

            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target === modal) closeModal();
                });
            }

            if (payCod) {
                payCod.addEventListener('click', function () { submitWithMethod('cod'); });
            }
            if (payVnpay) {
                payVnpay.addEventListener('click', function () { submitWithMethod('vnpay'); });
            }
        })();
    </script>
@endpush
